<?php
namespace App\Hooks;

use Themosis\Hook\Hookable;
use Themosis\Support\Facades\PostType;

class Books extends Hookable
{
    public function register()
    {
        $postTypeBook = PostType::make('books', 'Books', 'Book')
        ->setTitlePlaceholder('Insert the book title here...')
        ->setArguments([
            'supports' => ['title', 'editor'],
        ])
        ->set();
    }
}