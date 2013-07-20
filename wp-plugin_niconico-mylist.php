<?php 
/*
Plugin Name: WP Niconico Mylist Plugin
Plugin URI: http://www.absolute-keitarou.net/blog/
Description: ニコニコ動画マイリスト表示プラグイン
Author: keitarou
Version: 1.0
Author URI: http://www.absolute-keitarou.net/blog/
*/
$NiconicoMylist = new NiconicoMylist();
class NiconicoMylist{

	private $name        = 'NiconicoMylist';
	private $option_name = 'NiconicoMylist';
	private $baseurl     = 'http://www.nicovideo.jp/mylist/xxxxxidxxxxx?rss=2.0&numbers=1';

	public function __construct() {

		// ショートコードのタグと対応するメソッド記述
		add_shortcode( 'nicomy', array( $this, 'shortCodeNicomy' ) );

	}


/**
	ショートコードのメソッド
*/
	function shortCodeNicomy($params) {

		extract(shortcode_atts(array(
			'id'     => false, 
			'sort'   => "desc",
			'limit'  => 100,
			'top'    => true,
			"bottom" => true
		), $params));

		// フィードの取得
		$url = str_replace("xxxxxidxxxxx", $id, $this->baseurl);
		$rss = get_object_vars(simplexml_load_file($url));

		// 配列作成
		$items = array();
		foreach ($rss["channel"]->item as $key => $item){
			$item = get_object_vars($item);
			// 外部プレイヤ用のリンク作成。
			$item["player_link"] = str_replace("http://www.nicovideo.jp/watch/", 
												"http://ext.nicovideo.jp/thumb_watch/", 
												$item["link"]);
			$items[] = $item;
		}

		// ソートする
		if($sort == "asc"){
			$items = array_reverse($items);
		}

		// マイリスト用の埋め込み表示
		if($top) require "shortCodeNicomy2.html";

		// 動画を表示する
		foreach ($items as $key => $item) {
			if($limit>0){
				require "shortCodeNicomy1.html";
				$limit--;
			}
		}

		// マイリスト用の埋め込み表示
		if($bottom) require "shortCodeNicomy2.html";

		return "";
	}
}
