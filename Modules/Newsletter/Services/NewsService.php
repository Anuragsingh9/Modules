<?php

namespace Modules\Newsletter\Services;
use App\Services\StockService;
use App\Workshop;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Modules\Newsletter\Entities\News;
use Exception;
use Modules\Newsletter\Entities\NewsNewsletter;
use Modules\Newsletter\Exceptions\CustomValidationException;
use Workflow;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

/**
 * This class is performing all the actions of News
 * This class is being called from NewsController
 * Class NewsService
 * @package Modules\Newsletter\Services
 */
class NewsService {
    private $core;
    private $service;

    /**
     * @return static|null
     */
    public static function getInstance() {

        static $instance = NULL;
        if (NULL === $instance) {
            $instance = new static();
        }
        return $instance;
    }

    /**
     * @return CoreController
     */
    public function getCore() {
        if ($this->core){
            return $this->core;
        }
        return  app(\App\Http\Controllers\CoreController::class);
    }

    /**
     * @return StockService
     */
    public function getService() {
        if ($this->service){
            return $this->service;
        }
        return  app(\App\Services\StockService::class);
    }

    /**
     * @return mixed
     * @throws CustomValidationException
     */
    public function getNewsLetterWorkshop(){
        $workshop = Workshop::where('code1','=','NSL')->first();
        if(!$workshop){
            throw new CustomValidationException('auth','','message');
        }
        return $workshop;
    }

    public function addOrder(){
        $pre ='aaacb';
        $next = '';
        return $this->getLexoRank($pre,$next);
    }

    /**
     * @param $param
     * @return mixed
     * @throws Exception
     */
    public function createNews($param) {
        $param= $this->uploadNewsMedia($param); //uploading media according to the media_type in $param
        $news = News::create($param);
        if (!$news) {
            throw new CustomValidationException('news_create','news','message');
        }
        return $news;
    }


    public function getNewsByStatus($status){ // get all news of a given status
        if($status == 'validated'){
            

            return  News::with('newsLetterSentOn','reviewsCountByvisible','validatedOn')->where('status','=','validated')
                ->orWhere('status','=','sent')->get();
        }
        elseif ($status == 'rejected'){
            return News::with('validatedOn')->where('status','=','rejected')->get();
        }
        else{
            return  News::where('status',$status)->get();
        }
    }

    /**
     * @param $newsId
     * @return mixed
     * return a single news
     * @throws CustomValidationException
     */
    public function getNewsById($newsId){
       $news = News::where('id',$newsId)->first();
       if (!$news){
           throw new CustomValidationException('exists','news');
       }
       else {
           return $news;
       }
    }
    
    /**
     * @param $id
     * @param $param
     * @return News
     * @throws Exception
     */
    public function update($id, $param) { // updating news
        $param= $this->uploadNewsMedia($param);
        $news = News::where('id', $id)->update($param);
        if (!$news) {
            throw new CustomValidationException('exists','news');
        }
        return  News::find($id);
    }

    /**
     * @param $filePath
     * @param $file
     * @param $visibility
     * @return mixed
     */
    public function fileUploadToS3($filePath, $file, $visibility) {
        // TODO
        $domain = "Localhost";
//        $domain = Locahost::getInstance()->getHostname()->fqdn;

        return $this->getCore()->fileUploadToS3(
            "$domain/$filePath",$file,$visibility);
    }
    /**
     * @param $param
     * @return array
     */
    public function uploadNewsMedia($param) { // upload media according to media_type
        if(isset($param['request_media_type'])) {
            $path = config('newsletter.s3.news_image');
            $visibility = 'public';
            if ($param['request_media_type'] == Config::get('nl_const.news_media_video')) { // video uploading
                $param ['media_url'] = $param['request_media_url'];
                $param['media_type'] =Config::get('nl_const.news_media_video');
                $param['media_thumbnail'] = $this->fileUploadToS3($path,$param['request_media_blob'],$visibility);
            } elseif ($param['request_media_type'] == Config::get('nl_const.news_media_image')) { // image from system uploading
                $param['media_type'] = Config::get('nl_const.news_media_image');
                $param ['media_url'] = $this->fileUploadToS3($path,$param['request_media_blob'],$visibility);
                $param['media_thumbnail'] = NUll;
            } else{ // media_type == 2 and adobe image uploading so we already have url,
                $param['media_type'] = Config::get('nl_const.news_media_stock');
                $param ['media_url'] = ($param['request_media_url']);
                $param['media_thumbnail'] = NULL;
            }
            // unset these value as they are not in fillables
            unset ($param['request_media_url'],$param['request_media_blob'],$param['request_media_type']);
        }
        return $param;
    }



    /**
     * @param $request
     * @return array
     */
    public function uploadStockImage($request){
        $cores=$this->getCore();
        $stockService=$this->getService();
        $domain = 'localhost';
        $path = config('newsletter.s3.news_image');
        $visibility = 'public';
        $path=$stockService->uploadImage($request,$path,$visibility);
        $mediaUrl= $cores->getS3Parameter($path);
        return [
            'url'=>$mediaUrl,
            'path'=>$path,
        ];
    }

    /**
     * @param integer $newsId
     * @param string $transitionName
     * @return News
     */

    public function applyTransitions($newsId, $transitionName) {
        $news = News::find($newsId);
        if($transitionName == 'validate'){
            $this->addValidationDateToMeta($news);
        }elseif($transitionName == 'reject'){
            $this->addRejectedDateToMeta($news);
        }
//        dd($news);
        $workflow = Workflow::get($news,'news_status');

        $workflow->apply($news, $transitionName); // applying transition
        $news->save();
        return $news;
    }

    /**
     * @param $news
     */
    public function addValidationDateToMeta($news){
        $modelMeta = $news->validatedOn()->first();
        if($modelMeta == NULL){
            $modelMeta = [
                'fields'    => ['validated_on' =>Carbon::now()],
            ];
             $news->validatedOn()->create($modelMeta);
        }
        else{
            $previousData = $modelMeta->fields;
            $previousData['validated_on'] = Carbon::now();
            $modelMeta->fields = $previousData;
            $modelMeta->save();
        }
    }

    /**
     * @param $news
     */
    public function addRejectedDateToMeta($news){
        $modelMeta = $news->validatedOn()->first();
        if($modelMeta == NULL){
            $modelMeta = [
                'fields'    => ['rejected_on' =>Carbon::now()],
            ];
             $news->validatedOn()->create($modelMeta);
        }
        else{
            $previousData = $modelMeta->fields;
            $previousData['rejected_on'] = Carbon::now();
            $modelMeta->fields = $previousData;
            $modelMeta->save();
        }
    }

    /**
     * @param $param
     * @return mixed
     * @throws CustomValidationException
     */
    public function newsToNewsLetter($param){
           $pastNewsletter = News::with('newsLetterSentOn')->where('id',$param['news_id'])->first();
            if(count($pastNewsletter->newsLetterSentOn)== 0){
                return NewsNewsletter::create($param);
            }
                throw new CustomValidationException('newsletter_sent','news','message');
    }

    /**
     * @return mixed
     * return all validated news except those attached with Newsletter
     */
    public function getReservoirNews($newsletterId){
        return $reservoirNews =  News::whereDoesntHave('newsletter', function($query) use ($newsletterId){
            $query->where('newsletter_id',$newsletterId);
        })->whereDoesntHave('letterSentOn')
            ->where('status','=','validated')->orderBy('order_by','asc')->get();
    }

    public function customSorting($newsId,$newRank,$newsletterId)
    {
        $reservoirNews =  News::whereDoesntHave('newsletter', function($query) use ($newsletterId){
            $query->where('newsletter_id',$newsletterId);
        })->whereDoesntHave('letterSentOn')
            ->where('status','=','validated')->orderBy('order_by','asc')->get();

        $newsRank = $this->getPositionOfNews($newsId,$reservoirNews) + 1;
        $middleRank = $this->getBoundaryOrderBy($newsRank,$newRank,$reservoirNews);
        News::where('id',$newsId)->update(['order_by'=>$middleRank]);
        return $reservoirNews;
    }

    public function getPositionOfNews($newsId,$reservoirNews){
        $total = $reservoirNews->count();
        $news =$reservoirNews->where('id', $newsId)->first();
        $currentOrderBy = $news->order_by;

        foreach ($reservoirNews as $position => $record ) {
            for ($i = $position;$i <= $total;$i++){
                if ($record->order_by == $currentOrderBy){
                    return $currentRank = $i;
                }
            }
        }
    }

    public function getBoundaryOrderBy($currentRank,$newRank,$reservoirNews){
        $total = $reservoirNews->count();
        if ($currentRank < $newRank){
            $i = $newRank;
            if ($i != null) {
                $preOrderBy   = $reservoirNews[$i]['order_by'];
                $i = $newRank;
                $i = $i + 1;
                if ($i == $total ){
                    $nextOrderBy =  Config::get('nl_const.lexo_rank_max');
                }else{
                    $nextOrderBy = $reservoirNews[$i]['order_by'];
                }
            }
            return $this->getLexoRank($preOrderBy,$nextOrderBy);

        }elseif ($currentRank > $newRank) {
            $i = $newRank;
            if ($i != null) {
                $nextOrderBy = $reservoirNews[$i]['order_by'];
                if ($newRank == 0){
                    $preOrderBy = Config::get('nl_const.lexo_rank_min');
                }else{
                    $i = $newRank - 1;
                    $preOrderBy = $reservoirNews[$i]['order_by'];
                }
            }
            return $this->getLexoRank($preOrderBy,$nextOrderBy);
        }
    }


    public function getLexoRank($prev = null, $next = null) {
        // if prev null will assume in very first, if next null we'll assume at the end
        // boundary testing care.
        $prev = $prev == null ? Config::get('nl_const.lexo_rank_min') : $prev;
        $next = $next == null ? Config::get('nl_const.lexo_rank_max') : $next;

        // as between 'a' and 'b' we will need 'an' so for that we need to make string compare like
        // between a0 and b0 so we get an
        $strLen = $this->getGreaterStringLength($prev, $next) + 1;

        // making prev and next to append the a in prev, z in next
        // reason when we need to find between same length and next to each other like
        // b and c so it will like finding between ba and cz ->
        // no to care it will not make like ca cb cc.... cz so order will have prefix b -> bX will be result
        // e.g. 2 -> between baaa and baab  then -> baaam
        $prev = $this->addLexoStrPad($prev, $strLen, true);
        $next = $this->addLexoStrPad($next, $strLen, false);
        return $this->findRankBetween($prev, $next);
    }
    /*
     * HELPER METHODS
     */
    /**
     * This helper method returns the greatest string list among variable string parameters
     *
     * @param string ...$strings
     * @return int
     */
    public function getGreaterStringLength(...$strings) {
        $count = 0;
        foreach ($strings as $string) {
            $i = strlen($string);
            if ($i > $count) {
                $count = $i;
            }
        }

        return $count;
    }
    /**
     * adds the extra digit to string for finding between two consecutive character or lexo
     *
     * @param $string
     * @param $strLen
     * @param $min
     * @return string
     */
    public function addLexoStrPad($string, $strLen, $min) {
        $minMax = ($min ? 'min' : 'max');
        return str_pad($string, $strLen,  Config::get("nl_const.lexo_rank_$minMax"));
    }
    /**
     * actually finding rank between two equal length strings
     *
     * @param $prev
     * @param $next
     * @return string
     */
    public function findRankBetween($prev, $next) {
        $len = strlen($prev);
        $rank = '';
        for ($i = 0; $i < $len; $i++) {
            if ($prev[$i] == $next[$i]) {
                $rank .= $prev[$i];
            } else {
                $mid = $this->findMiddleChar($prev[$i], $next[$i]);
                $rank .= $mid;
                if ($mid != $prev[$i]) {
                    break;
                }
            }
        }
        return $rank;
    }
    public function findMiddleChar($i, $j) {
        return chr((int)((ord($i) + ord($j)) / 2));
    }


    /**
     * @param $id
     */
    public function delete($id){ // deleting all reviews of given news when news is deleted
        $news=News::find($id);
        $news->reviews()->delete(); // delete all reviews of given news
        $news->delete(); // deleting news
    }

    /**
     * @param $newsId
     * @param $newsLetterID
     */
    public function deleteNewsLetter($newsId,$newsLetterID){ // delete news to newsletter relation
        $news=NewsNewsletter::where('news_id',$newsId)->where('newsletter_id',$newsLetterID)->first();
        $news->delete();
    }


}


