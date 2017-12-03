用來選取範圍將Excel資料轉換成Array或是SQL Insert語法，或是Lravel的Query Builder.

DEMO:

[![Everything Is AWESOME](https://www.ccc.tc/Excelify.png)](https://youtu.be/LkaWIOUlOFU "Everything Is AWESOME")

<h3>請使用套件的方式安裝:</h3>

1. create new laravel project or using exist one. 

2. composer require deviny/excelify

3. php artisan vendor:publish

4. open project url with browser: https://yourdomain/excelify


<h3>環境變數</h3>

您可加入EXCELIFY_SECRET到.env中，即可上鎖畫面。

EXCELIFY_SECRET=secret

可以指定為空白，取消預設的RENDERTRON網址。

使用RENDERTRON時，可讓excelify抓取用JavaScript產生的表格，並將其轉換為Excel。

XCELIFY_RENDERTRON_URL=

<h3>為了確保執行上沒什麼問題，建議設定如下:</h3>

php.ini建議設定:
請勿載入xdebug.
<pre>
upload_max_filesize = 50M
post_max_size = 50M
max_execution_time = 0
memory_limit = -1
</pre>

nginx的設定:
<pre>
client_max_body_size 50m;
</pre>

<h3>Docker-Excelify:</h3>

對於Laravel或php.ini的設定不熟悉嗎？

您也可以使用Docker獨立運行的版本，電腦有安裝docker即可以運作了:

https://github.com/DevinY/dexcel

.env中可以加入EXCELIFY_SECRET，除API外，進行簡易密碼保護，只允許API的使用
預設為空白，可參考.env.example
