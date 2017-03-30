<?php
require '/opt/lampp/htdocs/todoApp/vendor/curl-master/src/Builder.php';


class CurlService {

    /**
     * @param $url string   The URL to which the request is to be sent
     * @return \Ixudra\Curl\Builder
     */
    public function to($url)
    {
        $builder = new Builder();

        return $builder->to($url);
    }

}
