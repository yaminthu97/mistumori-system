{{$result['name']}}様

いつも{{$result['system_name']}}をご利用いただきましてありがとうございます。

@if(isset($result['recruit_name']) && !empty($result['recruit_name']))
募集名：{{$result['recruit_name']}}
@endif
@if(isset($result['file_name']))
アップロードファイル名：{{$result['file_name']}}
@endif
@if($result['status'] === "OK")
画面から操作された{!!$result['process_name']!!}処理が完了しました。

今回の処理結果は下記となります。
処理結果：{{$result['status']}}
処理件数：{{number_format($result['process_count'])}}件
成功件数：{{number_format($result['successful_count'])}}件
@else
画面から操作された{!!$result['process_name']!!}処理にてエラーが発生しました。
システム管理者にご連絡いただけますようお願いいたします。

今回の処理結果は下記となります。
処理結果：{{$result['status']}}
@if(isset($result['error']))
@foreach($result['error'] as $msg)
{{$msg}}
@endforeach
@endif
@endif
-------------------------------------------
※本メールはご登録いただいたメールアドレス宛に送信されています。
※このメールにお心あたりがない場合は、お手数おかけいたしますが本メールを破棄
していただけますようお願いいたします。
※本メールは送信専用です。ご返信いただきましてもお答えできませんので、ご了承ください。
-------------------------------------------
