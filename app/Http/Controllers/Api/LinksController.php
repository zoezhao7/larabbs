<?php

namespace App\Http\Controllers\Api;

use App\Models\Link;
use Illuminate\Http\Request;
use App\Transformers\LinkTransformer;

class LinksController extends Controller
{
    public function index(Link $link)
    {
        $links = $link->getAllCacheLinks();
        return $this->response->collection($links, new LinkTransformer());
    }
}
