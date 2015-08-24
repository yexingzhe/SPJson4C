# SPJson4C
a project to make static packagist composer server mirror 
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
