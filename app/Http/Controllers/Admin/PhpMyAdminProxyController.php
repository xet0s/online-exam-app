<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class PhpMyAdminProxyController extends Controller
{

    public function proxy(Request $request, $path = '')
    {

        if (!auth()->user() || !auth()->user()->isAdmin()) {
            abort(403, 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
        }

        $targetUrl = 'http://phpmyadmin/' . $path;

        $queryString = $request->getQueryString();
        if ($queryString) {
            $targetUrl .= '?' . $queryString;
        }

        $headers = [];
        $headersToForward = [
            'Accept',
            'Accept-Language',
            'Content-Type',
            'User-Agent',
            'X-Requested-With',
        ];

        foreach ($headersToForward as $headerName) {
            if ($request->hasHeader($headerName)) {
                $headers[$headerName] = $request->header($headerName);
            }
        }

        $cookiesToForward = [];
        foreach ($_COOKIE as $name => $value) {
            if (str_starts_with($name, 'pma') || $name === 'phpMyAdmin') {
                $cookiesToForward[] = "$name=$value";
            }
        }
        if (!empty($cookiesToForward)) {
            $headers['Cookie'] = implode('; ', $cookiesToForward);
        }

        $method = strtoupper($request->method());
        $body = $request->getContent();

        $client = new Client(['timeout' => 10]);
        try {
            $response = $client->request($method, $targetUrl, [
                'headers'         => $headers,
                'body'            => $body,
                'allow_redirects' => false,
            ]);
        } catch (\Throwable $e) {
            if ($e instanceof RequestException && $e->hasResponse()) {
                $response = $e->getResponse();
            } else {
                abort(502, 'phpMyAdmin servisine erişilemiyor.');
            }
        }

        $status  = $response->getStatusCode();
        $content = $response->getBody()->getContents();
        $contentType = $response->getHeaderLine('Content-Type');

        if ($path === '' && str_contains($contentType, 'text/html')) {
            $baseTag = '<base href="/admin/phpmyadmin/">';
            $content = str_replace('<head>', '<head>' . $baseTag, $content);
        }

        $laravelResponse = response($content, $status);

        foreach ($response->getHeaders() as $name => $values) {
            $lowerName = strtolower($name);

            if ($lowerName === 'location') {

                $location = $values[0];
                if (str_starts_with($location, 'http://phpmyadmin/')) {
                    $location = str_replace('http://phpmyadmin/', '/admin/phpmyadmin/', $location);
                } elseif (str_starts_with($location, '/') && !str_starts_with($location, '/admin/phpmyadmin/')) {
                    $location = '/admin/phpmyadmin/' . ltrim($location, '/');
                } elseif (!str_starts_with($location, 'http') && !str_starts_with($location, '/')) {
                    $location = '/admin/phpmyadmin/' . $location;
                }
                $laravelResponse->header($name, $location);

            } elseif ($lowerName === 'set-cookie') {
                foreach ($values as $cookieValue) {
                    $laravelResponse->header($name, $cookieValue, false);
                }

            } elseif (in_array($lowerName, ['content-type', 'content-disposition', 'pragma', 'cache-control', 'expires'])) {
                $laravelResponse->header($name, $values[0]);
            }
        }

        return $laravelResponse;
    }
}
