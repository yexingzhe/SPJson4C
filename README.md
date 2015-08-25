# SPJson4C
a project to make static packagist composer server mirror 
##建立composer服务器最快最好的方案

做php开发的感受
作者从最初的玩discuz论坛到二次开发ptsource代码入门php，php是那个很简单的语言，混编的过程式代码像白开水一样一口气可以喝一壶。
后来先后接触ThinkPHP、CI、Yii、laravel，我们知道混编和过程式代码的不可取之处，取而代之的时单一入口文件和MVC、MVVM、模版系统等等。
面向对象、namespace等知识让做开发这件事越来越复杂，开发已深陷业务逻辑，基础库实现落后却无精力维护。
直到用上composer，我发现php又变简单了。
基础库的不足交给热门开源库 ，文档维护工作量大大降低了！！！！
把平时极少维护的代码抽离出来，让开发过程中的代码量降低，就是提高生产力！！！

as composer users know when composer do his job,he'll get meta data from the json packages on the remote server to solve dependents
so the faster you get the packges the fast you finish all your composer job.

composer工作时会从服务端获取包的信息来处理依赖，所以包信息文件的获取速度直接影响composer整个进程的速度


ex: thought a project which has 100 packages in use,
in intranet envroment you get a package downloaded using 50ms,then you get all of it using 5 seconds;
in internet enrroment you get a packagedownloaded using 500ms,then you get all of it using 50 seconds;

按一个项目100个包的依赖，
在局域网里一个包的下载时间平均50ms，100个包用时5s，
在互联网里一个包的下载时间平均500ms，100个包用时50s。


use it with Toran,you can edit the code to make it read json packges from local dictory other than proxy to packagist,it make a boost to composer downloading progress

配合Toran使用，修改部分代码让toran到本地目录中读取json文件，toran不再通过代理形式获取json文件,composer下载速度直接逆天了

