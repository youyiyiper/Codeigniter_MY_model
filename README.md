# Codeigniter_MY_model

Codeigniter_MY_model  超级model的建立和使用，
分享下最近在开发中用CI框架在编写model时遇到的一些困惑,和一些优化改进吧!_^_^_

####困惑：
1、没个model中的都有增删改查的的操作，每次都得针对不的表写增删改查

####优化改进：
1、在MY_model中写增删改查可供每个model来继承，就减少了重复事情

###安装方法:
把application/core/MY_model.php和application/core/MY_Controller.php 放到你应用同样的位置即可：

###详细：
代码中已经有非常详细的注释，请直接查看源代码和相关使用事例。

###说明：
由于该用法是从项目中抽取出来并修改了一些名称和注释,如有错误，请认真调试或修改来符合你的需求,
你也可以在此基础上继续封装你想要共用的方法！.^_^.