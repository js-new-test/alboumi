<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class PhotoBooks extends Model
{
	public $table = 'photo_books';

	public static function getBooks($langId)
	{
			$books = PhotoBooks::select('photo_books.title','photo_books.image','photo_books.link','photo_books.price','photo_books.description','products.product_slug')
											->join('products','products.id','=','photo_books.link')
											->where('photo_books.language_id',$langId)
											->where('photo_books.status','1')
											->whereNull('photo_books.deleted_at')
											->orderBy('photo_books.sort_order','ASC')
											->get();

			if(count($books) == 0 )
			{
					$defaultLanguageData = GlobalLanguage::with('language')->where('is_default',1)->where('status',1)->first();
					$defaultLanguageId = $defaultLanguageData['id'];
					$books = PhotoBooks::select('photo_books.title','photo_books.image','photo_books.link','photo_books.price','photo_books.description','products.product_slug')
													->join('products','products.id','=','photo_books.link')
													->where('photo_books.language_id',$defaultLanguageId)
													->where('photo_books.status','1')
													->whereNull('photo_books.deleted_at')
													->orderBy('photo_books.sort_order','ASC')
													->get();
			}
			return $books;
	}
}
