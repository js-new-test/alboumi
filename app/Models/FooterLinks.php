<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterLinks extends Model
{
    protected $table = 'footer_links';

    protected $fillable = ['fb_link','insta_link','youtube_link','twitter_link'];

    public static function getSocialLinks()
    {
        $footerLinks = FooterLinks::select('fb_link','insta_link','youtube_link','twitter_link')
                                    ->first();
        return $footerLinks;
    }
}
