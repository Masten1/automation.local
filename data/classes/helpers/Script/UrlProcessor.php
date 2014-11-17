<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dmitry Kukarev
 * Date: 30.07.12
 * Time: 22:13
 */

class Script_UrlProcessor extends Script_Endless {
    const CRON_CALLABLE = true;


    private $allCount = 0;
    private $verifiedCount = 0;
    private $unverifiedCount = 0;
    private $stopListCount = 0;

    function __construct(){
        $this->perIteration = 5000;
        parent::__construct();
    }

    function getQuery() {
        static $query;

        if( empty($query) ){
            $query = Url::getManager()
                ->select()
                ->join("projectSite", "ps")
                ->where('processed = 0')
                ->andWhereNotIn('root.status', array(URL::STATE_BLACKLIST, URL::STATE_STOP))
                ->andWhereIn("ps.status", array( ProjectSite::STATE_UNRECOGNIZED, ProjectSite::STATE_PIRATES ));
        }

        return $query;
    }

    /**
     * @param Url $url
     */
    function executeIteration($url) {
        $this->allCount++;
        $url->retry = $url->retry->get() + 1;
        if( $url->retry->get() > 5 ) {
            $url->status = Url::STATE_STOP;
        } else{
            switch( $url->status->get() ) {
                case URL::STATE_BLACKLIST:
                case URL::STATE_MANUAL_ADDED:
                case URL::STATE_STOP:
                    break;

                default:
                    //$this->log->notice("Processing {$url->link->get()}");
                    /** @var $project Project */
                    $project = $url->projectSite->project;
                    /*
                                     * Код ниже поднадобится, если будет неоходимость обрабатывать само содержание ссылки.
                                     * Не удаляйте его , пожалуйста.
                                     *
                                     *
                                        $link = $url->link->get();

                                        $ch = curl_init();
                                        curl_setopt_array($ch, array(
                                                               CURLOPT_URL => $link,
                                                               CURLOPT_AUTOREFERER => true,
                                                               CURLOPT_FOLLOWLOCATION => true,
                                                               CURLOPT_MAXREDIRS => 30,
                                                               CURLOPT_RETURNTRANSFER => true,
                                                               ));
                                        $body = curl_exec($ch);
                                        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                                        //not "Success" codes before 200 and aftrer 206
                                        if($code>206 && $code<200) {
                                            continue;
                                        }

                                        // Stop words in body
                                        $sBodyWords = $url->findWords($body, $stopWords);

                                        // Verify words in body
                                        $vBodyWords = $url->findWords($body, $verifyKeywords);
                                    */

                    $stopWords = $project->stopKeywords->asArray();
                    $verifyKeywords = $project->verifyingKeywords->asArray();

                    // Stop words in title and snippet
                    $stopTitleWords = StringFunctions::findWords( strip_tags($url->title->get()), $stopWords);
                    $stopSnippetWords = StringFunctions::findWords( strip_tags($url->snippet->get()), $stopWords);

                    // Verify words in title and snippet
                    $verifyTitleWords = StringFunctions::findWords( strip_tags($url->title->get()), $verifyKeywords);
                    $verifySnippetWords = StringFunctions::findWords( strip_tags($url->snippet->get()), $verifyKeywords);

                    $url->verifyWords = $totalVerifyWords = array_unique(array_merge($verifyTitleWords, $verifySnippetWords));
                    $url->stopWords   = $totalStopWords = array_unique(array_merge($stopTitleWords, $stopSnippetWords));

                    if( count($totalVerifyWords) ) {
                        $this->verifiedCount++;
                        $url->relevancy = Round(10000 * count($totalVerifyWords) / count($verifyKeywords));
                        $this->log->notice("{$url->link->get()} verified. Relevancy is {$url->relevancy->get()}");
                    } else{
                        $this->unverifiedCount++;
                        $this->log->notice("{$url->link->get()} unverified");
                        $url->status = URL::STATE_STOP;
                        $url->relevancy = 0;
                    }

                    if( count($totalStopWords) ) {
                        $this->stopListCount++;
                        $this->log->notice("{$url->link->get()} in stop list");
                        $url->status = URL::STATE_STOP;
                    }

                    break;
            }
        }

        $url->processed = true;
        $url->save();
    }

    function finally() {
        if( $this->itemsCount )
            $this->log->notice("Found {$this->allCount} links. {$this->verifiedCount} verified, {$this->unverifiedCount} unverified, {$this->stopListCount} in stoplist");
    }

    function prepare() {
        $this->allCount = 0;
        $this->verifiedCount = 0;
        $this->unverifiedCount = 0;
        $this->stopListCount = 0;
    }
}

