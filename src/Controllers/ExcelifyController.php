<?php

namespace Deviny\Excelify\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;
use Validator;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
//use Log;

class ExcelifyController extends Controller
{
    protected $objPHPExcel;
    protected $tablename;
    protected $tablenum;
    protected $rowData;
    protected $download_name;
    protected $dom;
    protected $rendertron_url;

    function __construct() {
        $this->objPHPExcel = new Spreadsheet();
        $this->download_name = "download.xlsx";
   }

    public function index(){
        return view('excelify::index')
        ->with('tabnum',2);
    }
    public function __invoke(Request $r){

      //Input的資料驗證

        //記住input
        $r->flash();
        session(['tabnum' => '2']);

      $validator = Validator::make($r->all(), [
        'url' => 'required',
        ],[
        'url.required'=>'url|'.__('message.url required'),
        ])->validate();

        $sheet_index = 0;
        $this->tablename = empty($r->tablename)?"table":$r->tablename;
        $this->tablenum = empty($r->tablenum)?"0":$r->tablenum;
        $this->rendertron_url = ($r->rendertron_url!='')?$r->rendertron_url:'';
        //$cacheSettings = array( ' memoryCacheSize ' => '8MB');
        $objProps=$this->objPHPExcel->getProperties();
        $objProps->setCreator('excelify');

        $this->getData($r->url);

        if(!$this->dom) {
          return view('excelify::index')
          ->with('data', [])
          ->with('datatype', 'table')
          ->with('tablenum', $this->tablenum)
          ->with('tablename', $this->tablename);
        }

        $this->tableToExcel();


      if(!isset($this->rowData)) $this->rowData=[];
      return view('excelify::index')
      ->with('data', $this->rowData)
      ->with('datatype', 'table')
      ->with('tablenum', $r->tablenum)
      ->with('tablename', $this->tablename)
      ->with('tabnum',2);
  }
  public function getData($data){
    if(preg_match('#^https?://#us', $data)){

      if($this->rendertron_url!=""){
        $arrData = explode('?',$data);
        if(count($arrData)==1){
         $data = $this->rendertron_url.$arrData[0].'%3F'.time();
        }else{
         $data = $this->rendertron_url.$arrData[0].'%3F'.$arrData[1]."&".time();
        }
      }

        $http = new Client;
        $response = $http->get($data);
        $data = (string) $response->getBody();
    }

    $dom = new \DOMDocument();
    libxml_use_internal_errors(true); // 忽略解析錯誤
    $dom->loadHTML($data);
    libxml_clear_errors();

    $this->dom = $dom->getElementsByTagName('table');

  }

  public function excelify(Request $r, $tablenum=0){
      $this->tablenum = $tablenum;
      $this->getData($r->table);
      $this->tableToExcel();
      $name = isset($r->tablename)?$r->tablename:'download.xlsx';
      $this->download_name = sprintf("%s",$name);
      //$this->tablenum = empty($r->tablenum)?"0":$r->tablenum;
      return $this->download_temp();
  }

  protected function clear_temp_folder($times=0){

        //清除舊的暫存資料
        $folderName=sprintf("%s/app/excelfile/",storage_path());
        if (file_exists($folderName)) {
          foreach (new \DirectoryIterator($folderName) as $fileInfo) {
            if ($fileInfo->isDot()) {
              continue;
            }
            if ($fileInfo->isFile() && time() - $fileInfo->getCTime() >= $times) {
              unlink($fileInfo->getRealPath());
            }
          }
        }

  }

//轉存Table到Excel暫存
private function tableToExcel(){

        //Table資料
        if($this->tablenum==0){
            $arrTables = $this->dom;
            foreach($arrTables as $sheet_index=>$table) {
               $this->create_sheet($table, $sheet_index);
            }
        }else{
            $table = $this->dom[$this->tablenum-1];
            $this->create_sheet($table,0);
        }

        //如果目錄不存在就建資料夾
        if (!file_exists(storage_path().'/app/excelfile')) {
          mkdir(storage_path().'/app/excelfile', 0755, true);
        }

        //清除暫存
        $this->clear_temp_folder(2*24*60*60);


        //路徑初值
        $path=sprintf("excelfile/%s.xlsx", Str::random(10));


        //記錄path
        session(['path'=>$path]);

    //儲存Excel
      $this->objPHPExcel->setActiveSheetIndex(0);
      $objWriter = new Xlsx($this->objPHPExcel);
      $objWriter->save(storage_path()."/app/".$path);
      unset($this->objPHPExcel);
      unset($objWriter);

}

 protected function create_sheet($table, $sheet_index){
                $this->objPHPExcel->createSheet($sheet_index);
                $this->objPHPExcel->setActiveSheetIndex($sheet_index);

                $this->objPHPExcel->getActiveSheet()->setTitle($this->tablename.($sheet_index+1));

                $arrTr = $table->getElementsByTagName('tr');

                $activeSheet = $this->objPHPExcel->getActiveSheet();
                $this->rowData = [];
                //至少大於三行的才抓取
                    foreach($arrTr as $i=>$tr){
                        $arrTh = $tr->getElementsByTagName('th');
                        foreach($arrTh as $column_index=>$th){
                            $pureText = strip_tags(preg_replace('#<(span|div|label)+.*display:none.+</(span|div|label)+>#us', '', $th->nodeValue));
                            $this->rowData[$i][$column_index] = $pureText;
                            //欄位由0開始
                            //行由1開始
                            if(is_numeric($pureText)){
                                $activeSheet->getCellByColumnAndRow($column_index, $i+1)->setValueExplicit( $pureText , DataType::TYPE_NUMERIC);
                            }else{
                                $pureText = htmlspecialchars_decode($pureText);
                                $pureText = preg_replace('/&nbsp;/uim', '', $pureText);
                                $activeSheet->getCellByColumnAndRow($column_index, $i+1)->setValueExplicit( $pureText , DataType::TYPE_STRING);
                            }
                        }

                        $arrTd = $tr->getElementsByTagName('td');
                        //dd($arrTd);
                        foreach($arrTd as $column_index=>$td){
//                            var_dump($td->innertext);
                            //移除所有隱藏的tag
                            $pureText = strip_tags(preg_replace('#<(span|div|label)+.*display:none.+</(span|div|label)+>#us', '', $td->nodeValue));

                            $this->rowData[$i][$column_index] = $pureText;
                            //欄位由0開始
                            //行由1開始
                            if(is_numeric($pureText)){
                                $activeSheet->getCellByColumnAndRow($column_index, $i+1)->setValueExplicit( $pureText , DataType::TYPE_NUMERIC);
                            }else{
                                $pureText = htmlspecialchars_decode($pureText);
                                $pureText = preg_replace('/&nbsp;/uim', '', $pureText);
                                $activeSheet->getCellByColumnAndRow($column_index, $i+1)->setValueExplicit( $pureText , DataType::TYPE_STRING);
                            }
                        }
                    }
 }
 //下載轉存的Excel
 function download_temp(){
//  dd(session()->get('path'));
  return response()
  ->download(storage_path()."/app/".session()->get('path'), $this->download_name);
 }
}

