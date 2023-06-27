<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhotographerPortfolio extends Model
{
    protected $table = 'photographer_portfolio';

    protected $fillable = ['photographer_id','image','product_id','sort_order','status','deleted_at'];
    // To get photgrapher's portfolio : By Nivedita 21-01-2021 */
    public static function getPortfolioByPhotographer($photgrapherID) {
      $portfolioArr=\App\Models\PhotographerPortfolio::select('photographer_portfolio.id','photographer_portfolio.image','photographer_portfolio.product_id','P.product_slug')
      ->join('products as P','P.id','=','photographer_portfolio.product_id')
      ->where('photographer_portfolio.photographer_id', $photgrapherID)
      ->where('photographer_portfolio.status', 1)
      ->whereNull('photographer_portfolio.deleted_at')
      ->orderBy('photographer_portfolio.sort_order', 'asc')
      ->get()
      ->toArray();
      return $portfolioArr;
    }
    public static function getPortfolioCount($photgrapherID) {
      $portfolioCount=\App\Models\PhotographerPortfolio::select('id','image','product_id')
      ->join('products as P','P.id','=','photographer_portfolio.product_id')
      ->where('photographer_portfolio.photographer_id', $photgrapherID)
      ->where('photographer_portfolio.status', 1)
      ->whereNull('photographer_portfolio.deleted_at')
      ->count();
      return $portfolioCount;
    }
}
