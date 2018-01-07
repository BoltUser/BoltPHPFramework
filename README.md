# BoltPHP
**Fast PHP Framework**

How to use Cache :
https://github.com/PHPSocialNetwork/phpfastcache/tree/final/docs/examples


http://propelorm.org/blog/2010/02/16/propel-query-by-example.html


public function home(){
        $content = '<h1>Hello World</h1>';
        $content .= 'Hello ' . $this->request->params->get('name');
        $this->response->content->set($content);
        echo $this->response->content->get();
}