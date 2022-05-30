<?php

require __DIR__ . "/.env";

	// 設定項目
	$redirect_uri = ( !isset($_SERVER['HTTPS']) || empty($_SERVER['HTTPS']) ? 'http://' : 'https://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ;		// このプログラムを設置するURL
 
	// セッションスタート
	session_start() ;

	// HTML用
	$html = '' ;

	// アプリ認証から帰ってきた時([return=1]があるアクセス)の場合はアクセストークンを取得
	// [手順3] ユーザーがアプリを許可して、アプリがアクセストークンを受け取る、の部分
	if( isset($_GET['return']) && is_string($_GET['return']) && $_GET['return'] == 1 )
	{
		// セッションにリクエストトークンがない場合は不正と判断してエラー
		if( !isset($_SESSION['code']) || empty($_SESSION['code']) )
		{
			$error = 'セッションが上手く機能してないか、手動で[return=1]を付けてアクセスしてます…。' ;
		}

		// セッションにCSRF対策用の[state]がない場合は不正と判断してエラー
		elseif( !isset($_SESSION['state']) || empty($_SESSION['state']) )
		{
			$error = 'セッションが上手く機能してないかもしれません。' ;
		}

		// アクセストークンの取得
		else
		{
			// // リクエスト用のコンテキストを作成
			// $context = array(
			// 	'http' => array(
			// 		'method' => 'POST' ,
			// 		'content' => http_build_query( array(
			// 			'consumer_key' => $consumer_key ,
			// 			'code' => $_SESSION['code'] ,
			// 		) ) ,
			// 	) ,
			// ) ;

            // $contents = file_get_contents($url, false, stream_context_create($options));

			// // CURLを使ってリクエスト
			// $curl = curl_init() ;

			// // オプションのセット
			// curl_setopt( $curl , CURLOPT_URL , 'https://getpocket.com/v3/oauth/authorize' ) ;
			// curl_setopt( $curl , CURLOPT_HEADER, 1 ) ; 
			// curl_setopt( $curl , CURLOPT_CUSTOMREQUEST , $context['http']['method'] ) ;			// メソッド
			// curl_setopt( $curl , CURLOPT_SSL_VERIFYPEER , false ) ;								// 証明書の検証を行わない
			// curl_setopt( $curl , CURLOPT_RETURNTRANSFER , true ) ;								// curl_execの結果を文字列で返す
			// curl_setopt( $curl , CURLOPT_POSTFIELDS , $context['http']['content'] ) ;			// リクエストボディ
			// curl_setopt( $curl , CURLOPT_TIMEOUT , 5 ) ;										// タイムアウトの秒数

			// // 実行
			// $res1 = curl_exec( $curl ) ;
			// $res2 = curl_getinfo( $curl ) ;

			// // 終了
			// curl_close( $curl ) ;

			// // 取得したデータ
			// $body = substr( $res1, $res2['header_size'] ) ;										// 取得したデータ(JSONなど)
			// $header = substr( $res1, 0, $res2['header_size'] ) ;								// レスポンスヘッダー (検証に利用したい場合にどうぞ)

			$url = 'https://getpocket.com/v3/oauth/authorize';
			$data = array(
				'consumer_key' => $consumer_key ,
				'code' => $_SESSION['code'] ,
			);
			$content = http_build_query($data);
			$opts = array(
				'http'=>array(
					  'method'=>"POST",
					  'content' => $content,
				),
			);
	// var_dump($opts);exit;
			$context = stream_context_create($opts);
			$body = file_get_contents($url, false, $context);
			$header = $http_response_header;
			// var_dump($body, $header);exit;

			// 関数[pocket_get_query_syncer]を使って、GETパラメータ形式の文字列を配列に変換
			$query = pocket_get_query_syncer( $body ) ;
 
			// CSRF対策
			if( !isset($query['state']) || empty($query['state']) || $query['state'] != $_SESSION['state'] )
			{
				$error = 'セッションに保存してあるstateと、返ってきたstateの値が違います…。' ;
			}

			// アクセストークンが取得できない場合はエラー
			elseif( !isset($query['access_token']) || empty($query['access_token']) )
			{
				$error = 'アクセストークンを取得できませんでした…。' ;
			}

			else
			{
				// アクセストークンを変数に格納
				$access_token = $query['access_token'] ;

				setcookie('token', $access_token, time()+60*60*24*7);

				// // 出力する
				// $html .=  '<h2>実行結果</h2>' ;
				// $html .=  '<dl>' ;
				// $html .=  	'<dt>ユーザーID</dt>' ;
				// $html .=  		'<dd>' . $query['username'] . '</dd>' ;
				// $html .=  	'<dt>アクセストークン</dt>' ;
				// $html .=  		'<dd>' . $access_token . '</dd>' ;
				// $html .=  '</dl>' ;
			}
		}

		// // 出力する
		// $html .= '<h2>取得したデータ</h2>' ;
		// $html .= '<p>下記のデータを取得しました。</p>' ;
		// $html .= 	'<h3>ボディ</h3>' ;
		// $html .= 	'<p><textarea rows="8">' . $body . '</textarea></p>' ;
		// $html .= 	'<h3>レスポンスヘッダー</h3>' ;
		// $html .= 	'<p><textarea rows="8">' . $header . '</textarea></p>' ;

		// // アプリケーション連携の解除
		// $html .= '<h2>アプリケーション連携の解除</h2>' ;
		// $html .= '<p>このアプリケーションとの連携は、下記設定ページで解除することができます。</p>' ;
		// $html .= '<p><a href="https://getpocket.com/connected_applications" target="_blank">https://getpocket.com/connected_applications</a></p>' ;

		// セッション終了
		$_SESSION = array() ;
		session_destroy() ;

		header('Location: list.php');
		exit;
	}

	// [return=1]がないアクセス(初回アクセス)の場合
	// [手順1] pocketからリクエストトークンを取得する、の部分
	else
	{
		// CSRF対策
		session_regenerate_id( true ) ;
		$state = sha1( uniqid( mt_rand() , true ) ) ;
		$_SESSION['state'] = $state ;

		// リダイレクトURLにパラメータを追加
		$redirect_uri .= '?return=1' ;

		// // リクエスト用のコンテキストを作成
		// $context = array(
		// 	'http' => array(
		// 		'method' => 'POST' ,
		// 		'content' => http_build_query( array(
		// 			'consumer_key' => $consumer_key ,
		// 			'redirect_uri' => $redirect_uri ,
		// 			'state' => $state ,
		// 		) ) ,
		// 	)
		// ) ;

		// // CURLを使ってリクエスト
		// $curl = curl_init() ;

		// // オプションのセット
		// curl_setopt( $curl , CURLOPT_URL , 'https://getpocket.com/v3/oauth/request' ) ;
		// curl_setopt( $curl , CURLOPT_HEADER, 1 ) ; 
		// curl_setopt( $curl , CURLOPT_CUSTOMREQUEST , $context['http']['method'] ) ;			// メソッド
		// curl_setopt( $curl , CURLOPT_SSL_VERIFYPEER , false ) ;								// 証明書の検証を行わない
		// curl_setopt( $curl , CURLOPT_RETURNTRANSFER , true ) ;								// curl_execの結果を文字列で返す
		// curl_setopt( $curl , CURLOPT_POSTFIELDS , $context['http']['content'] ) ;			// リクエストボディ
		// curl_setopt( $curl , CURLOPT_TIMEOUT , 5 ) ;										// タイムアウトの秒数

		// // 実行
		// $res1 = curl_exec( $curl ) ;
		// $res2 = curl_getinfo( $curl ) ;

		// // 終了
		// curl_close( $curl ) ;

		// // 取得したデータ
		// $body = substr( $res1, $res2['header_size'] ) ;										// 取得したデータ(JSONなど)
		// $header = substr( $res1, 0, $res2['header_size'] ) ;								// レスポンスヘッダー (検証に利用したい場合にどうぞ)

		$url = 'https://getpocket.com/v3/oauth/request';
		$data = array(
			'consumer_key' => $consumer_key ,
			'redirect_uri' => $redirect_uri ,
			'state' => $state ,
		);
		$content = http_build_query($data);
        $opts = array(
            'http'=>array(
	              'method'=>"POST",
				  'content' => $content,
			),
		);
// var_dump($opts);exit;
		$context = stream_context_create($opts);
		$body = file_get_contents($url, false, $context);
        $header = $http_response_header;
		// var_dump($body, $header);exit;

		// 関数[get_query_syncer]を使って、GETパラメータ形式の文字列を配列に変換
		$query = pocket_get_query_syncer( $body ) ;
 
		// リクエストトークンを取得できなければエラー
		if( !isset($query['code']) || empty($query['code']) )
		{
			$error = 'リクエストトークンが取得できませんでした。多分コンシューマーキーの設定が間違っています。' ;
		}
 
		// リクエストで送った[state]の値と、返って来た[state]の値が違ったらエラー
		elseif( !isset($query['state']) || empty($query['state']) || $state != $query['state'] )
		{
			$error = '不正なリクエスト、またはレスポンスです…。' ;
		}
		else
		{
			// セッションにリクエストトークンの値を格納しておく
			$_SESSION['code'] = $query['code'] ;
 
			// ユーザーをアプリ認証画面へアクセス(リダイレクト)させる
			// [手順2] ユーザーがそのリクエストトークンを持って、pocketの「アプリ認証画面」にアクセスする、の部分
			header( 'Location: https://getpocket.com/auth/authorize?request_token=' . $query['code'] . '&redirect_uri=' . $redirect_uri ) ;
		}
	}

	// エラー時の処理
	if( isset($error) || !empty($error) )
	{
		$html = '<p><mark>' . $error . '</mark>もう一度、認証をするには、<a href="' . explode( '?' , $_SERVER['REQUEST_URI'] )[0] . '">こちら</a>をクリックして下さい。</p>' ;
	}

	// GETクエリ形式の文字列を配列に変換する関数
	function pocket_get_query_syncer( $data = '' )
	{
		// 文字列を[&]で区切って配列に変換する
		$ary = explode( '&' , $data ) ;

		// [&]が含まれていない場合は終了
		if( 2 > count( $ary ) )
		{
			return false ;
		}

		// 文字列を配列に整形する
		foreach( $ary as $items )
		{
			$item = explode( '=' , $items ) ;
			$query[ $item[0] ] = $item[1] ;
		}

		// 返却
		return $query ;
	}

?>
<!-- 
<?php
	// ブラウザに[$html]を出力 (HTMLのヘッダーとフッターを付けましょう)
	echo $html ;

	// 6a21da2e-1a92-31bf-4411-e9b23c
?> -->

