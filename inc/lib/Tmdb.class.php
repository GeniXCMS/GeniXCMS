<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/*
*    GeniXCMS - Content Management System
*    ============================================================
*    Build          : 20140925
*    Version        : 0.0.1 pre
*    Developed By   : Puguh Wijayanto (www.metalgenix.com)
*    License        : MIT License
*    ------------------------------------------------------------
*    filename : Tmdb.class.php
*    version : 0.0.1 pre
*    build : 20140925
*/

class Tmdb
{
    var $apikey = '';
    var $config;

    function __construct($apikey){
        
        $this->apikey = $apikey;
        $this->config = $this->getConfig($apikey);
        //echo $this->apikey;
    }



    function getConfig($apikey){
        $url = "http://api.themoviedb.org/3/configuration?api_key=".$apikey;
        $config = $this->curl($url);
        return $config;
    }

    public function search($q, $page=''){
        $q = str_replace(' ', '+', trim($q));
        if(isset($page) && $page !=''){
            $page = "&page=".$page;
        }else{
            $page = "";
        }
        $url = "http://api.themoviedb.org/3/search/movie?query=".$q."&api_key=".$this->apikey.$page;
        $search = $this->curl($url);
        //echo $search;
        return $search;
    }

    public function getMovieData($id){
        $getMovie = $this->getMovie($id);
        $getCast = $this->getCast($id);
        $getImage = $this->getImage($id);
        $data = array_merge($getMovie, $getCast);
        $data = array_merge($data, $getImage);
        return $data;
    }
    public function getMovie($id){
        $url = "http://api.themoviedb.org/3/movie/".$id."?api_key=".$this->apikey;
        $movie = $this->curl($url);
        return $movie;
    }

    public function getCast($id){
        $url = "http://api.themoviedb.org/3/movie/{$id}/credits?api_key=".$this->apikey;
        $cast = $this->curl($url);
        return $cast;
    }

    public function getImage($id){
        $url = "http://api.themoviedb.org/3/movie/{$id}/images?api_key=".$this->apikey;
        $cast = $this->curl($url);
        return $cast;
    }

    public function getSimilar($id){
        $url = "http://api.themoviedb.org/3/movie/{$id}/similar?api_key=".$this->apikey;
        $similar = $this->curl($url);
        return $similar;
    }

    public function getLatest(){
        $url = "http://api.themoviedb.org/3/movie/latest?api_key=".$this->apikey;
        $latest = $this->curl($url);
        return $latest;
    }


    public function getUpcoming(){
        $url = "http://api.themoviedb.org/3/movie/upcoming?api_key=".$this->apikey;
        $upcoming = $this->curl($url);
        return $upcoming;
    }


    public function getPLaying(){
        $url = "http://api.themoviedb.org/3/movie/now_playing?api_key=".$this->apikey;
        $now_playing = $this->curl($url);
        return $now_playing;
    }

    public function getPopular(){
        $url = "http://api.themoviedb.org/3/movie/popular?api_key=".$this->apikey;
        $popular = $this->curl($url);
        return $popular;
    }

    public function getTopRated(){
        $url = "http://api.themoviedb.org/3/movie/top_rated?api_key=".$this->apikey;
        $top_rated = $this->curl($url);
        return $top_rated;
    }

    private function curl($url){
        $ca = curl_init();
        curl_setopt($ca, CURLOPT_URL, $url);
        curl_setopt($ca, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ca, CURLOPT_HEADER, FALSE);
        curl_setopt($ca, CURLOPT_HTTPHEADER, array("Accept: application/json"));
        $response = curl_exec($ca);
        curl_close($ca);
        //var_dump($response);
        $result = json_decode($response, true);
        return $result;
    }
}