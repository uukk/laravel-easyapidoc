# laravel-easyapidoc

## 简介
laravel-easyapidoc 适用于当前最流行的 Laravel 框架的一个扩展库. 便于 Laravel 用户可以很方便地使用它写文档注释.

## 安装

#### Composer 设置

首先添加 laravel-easyapidoc 包添加到你的 `composer.json` 文件的 `require` 里:

	"require": {
		    "xinran/laravel-easyapidoc": "dev-master"
	}

然后 运行 `composer update`


## 使用方法

1、添加路由

    Route::get('api', function () {
        $doc = new \XinRan\EasyApiDoc();
        return $doc->getApiDoc();
    });
    
2、在你需要写注释的方法上面写注释
    
3、地址加上 api 就可以访问了    
    

## 其它

如果接口只在特定环境下访问

    if (app()->environment('local')) {
       Route::get('api', function () {
           $doc = new \XinRan\EasyApiDoc();
           return $doc->getApiDoc();
       });
    }
        
## 注释参数说明

    /**
     * 这里是方法名
     * @name 这里是接口名
     * @group 这里是接口分组
     * @route 这里是路由
     * @method 这里是请求方法
     * @author 这里是作者
     * @description 这里是接口描述
     * @created 这里是接口创建时间 2016-10-25 16:21
     * @updated 这里是接口更新时间 2016-10-25 16:21
     * @param string(参数类型) param(参数名) param(参数说明)
     * @return string(返回参数类型)  message(返回参数) message(返回参数说明)
     * @return array data data
     * @return integer data.id id
     * @return string data.name name
     */
     
    eg.
    
    /**
      * 测试一下
      * @name 测试一下
      * @group 测试分组
      * @route route
      * @method get
      * @author xinran
      * @description 测试一下
      * @created 2016-10-25 16:33
      * @updated 2016-10-25 16:33
      * @param string param param
      * @return integer code code（0:success、other:failed）
      * @return string message message
      * @return array data data
      * @return integer data.id id
      * @return string data.name name
      */