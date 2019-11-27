# Git

### Git原理：
Git在每次提交更新时，都会浏览一遍所有文件的指纹信息(校验和)。如果发现哪个文件有变化，就对文件做一个快照，如果没有变化，就只对上次的快照做一个链接。
在保存到Git之前，所有数据都会进行SHA-1校验和计算，并将这个结果作为数据的索引和唯一标识。


### 在Git中任何文件都只有*三*种状态： 

+ 已修改 modified 
+ 已暂存 staged
+ 已提交 committed  

### 在Git中有三个区域：

- git directory : 本地仓库 repository
- staging area  : 暂存区
- working directory : 工作区

### Git特点：

1. 直接记录快照，而非差异比较  
2. 近乎所有操作都是本地执行  
3. 时刻保持数据完整性  checksum
4. 多数操作仅添加数据  
5. 文件的三种状态  

### Git配置：

系统配置目录： /etc/gitconfig
当前用户配置目录： ~/.gitconfig
当前项目配置目录： ~/.git/config


使用git命令行操作：
git clone <url>
设置当前project的userName/userEmail1
git config user.name "userName"
git config user.enail "userEmail"
设置全局userName/userEmail
git config --global user.name "userName"
git config --global user.email "userEmail"
查看远端地址：git remote ‐v
origin:本地默认的远端设置的名称
fetch：pull的远端地址
push：push的远端地址



# Git command
- 初始化版本库
1. git clone 克隆仓库
2. git init  新建仓库
3. git config --global --replace-all user.email "your email address"  修改全局用户email地址
4. git config --global --replace-all user.name "your name"  修改全局用户名称
- 提交到暂存空间
1. git add readme.txt 添加一个文件
2. git add . 把工作区文件提交到暂存区。提交的文件不包括删除的。
3. git add -u 把工作区文件提交到暂存区。提交的文件不包括 untracked file
4. git add -A 把所有改动提交到暂存区
- 提交已暂存文件
1. git pull origin test 拉取本地test代码
2. git commit -m '描述'
3. git status 查看修改文件
4. vim 文件（a.php/b.html）
5. git push origin test:master
提交本地test分支作为远程的master分支
- 新建分支
1. git branch -a 查看所有分支(包括远程分支)
2. git branch -l 查看所有已经创建的分支
3. git branch -v 查看分支的扩展详情
4. git branch -av 查看那所有分支的扩展详情
5. git fetch 更新所有分支
6. git branch 分支名称(lucy_test) 新建分支(lucy_test)
- 切换分支
1. git checkout 分支名称(lucy_test)
- 合并分支
1. git checkout master 从lucy_test分支切换到master
2. git merge 子分支(lucy_test)
在master分支上面合并子分支

git branch --set-upstream-to=   添加上游
