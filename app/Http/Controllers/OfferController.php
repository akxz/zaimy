<?php

namespace App\Http\Controllers;

use DB;
use App\Offer;
use App\Traits\OffersTrait;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class OfferController extends Controller
{
	use OffersTrait;
	
    public function index()
	{
		$link = DB::table('settings')->where('param', 'link')->first()->value;
		
		return view('zaimy_empty', ['link' => $link]);
	}
	
    public function base_cards()
	{
		$offers = Offer::where('active', 1)->orderBy('sort', 'desc')->take(10)->get();
		$link = DB::table('settings')->where('param', 'link')->first()->value;
		$data = ['offers' => $offers, 'link' => $link];
		$out = [
			'code' => $this->show_offers($data)
		];
		
		return $out;
	}
	
    public function filter(Request $request)
	{
		$link = DB::table('settings')->where('param', 'link')->first()->value;
		$query = Offer::where('active', 1);
		
		if (is_numeric($request->get('slf_summ'))) {
			$query->where('amount_max', '>=', $request->get('slf_summ'));
		}
		
		if (is_numeric($request->get('slf_time'))) {
			$query->where('term_max', '>=', $request->get('slf_time'));
		}
		
		if (is_numeric($request->get('slf_percent'))) {
			$query->where('daily_rate', '<=', $request->get('slf_percent'));
		}
		
		if (is_numeric($request->get('slf_age'))) {
			$query->where('age_min', '>=', $request->get('slf_age'));
		}
		
		if ($request->get('field')) {
			$field = $request->get('field');
		
			if ($field == 'km5') {
				$field = 'k5m';
			} elseif ($field == 'sum_max') {
				$field = 'amount_max';
			} elseif ($field == 'percent') {
				$field = 'daily_rate';
			} elseif ($field == 'title') {
				$field = 'name';
			}
			
			$sort = $request->get('sort_type');
			
			$query->orderBy($field, $sort);
		}
		
		$offers = $query->orderBy('sort', 'desc')->get();
		$data = ['offers' => $offers, 'link' => $link];
		$out = [
			'code' => $this->show_offers($data)
		];
		
		return $out;
	}
	
	public function notFoundRedirect()
	{
		$link = DB::table('settings')->where('param', 'link')->first()->value;
		
		return redirect()->away($link);
	}
}
