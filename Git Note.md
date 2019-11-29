# Git

## git --help
```
usage: git [--version] [--help] [-C <path>] [-c <name>=<value>]
           [--exec-path[=<path>]] [--html-path] [--man-path] [--info-path]
           [-p | --paginate | -P | --no-pager] [--no-replace-objects] [--bare]
           [--git-dir=<path>] [--work-tree=<path>] [--namespace=<name>]
           <command> [<args>]
```

### 这些是在各种情况下使用的通用Git命令:

##### 启动一个工作区域
   - clone      将存储库克隆到新目录中
   - init       创建一个空的Git存储库或重新初始化一个现有的存储库

##### 处理当前的更改
   - add        将文件内容添加到索引中
   - mv         移动或重命名文件、目录或符号链接
   - reset      将当前磁头复位到指定的状态
   - rm         从工作树和索引中删除文件

##### 检查历史记录和状态
   - bisect     使用二分查找查找引入错误的提交
   - grep       打印匹配一个正则模式的行数 Print lines matching a pattern 
   - log        显示提交日志
   - show       显示各种类型的对象
   - status     显示工作树状态 (增加、删除或修改了哪些文件......)

##### 标记和调整共同历史
   - branch     列出、创建或删除分支
   - checkout   切换分支或还原工作树文件
   - commit     记录对存储库的更改
   - diff       显示提交、提交和工作树等之间的更改
   - merge      将两个或多个开发历史连接在一起
   - rebase     在另一个基准点上重新应用提交 Reapply commits on top of another base tip
   - tag        创建、列出、删除或验证用GPG签名的标记对象

##### 协作
   - fetch      从另一个存储库下载对象和引用到本地仓库
   - pull       从另一个存储库或本地分支获取并与之集成
   - push       更新远程引用以及相关的对象

- 'git help -a' and 'git help -g' 列出可用的子命令和一些概念指南。
- 参见 'git help <command>' or 'git help <concept>' 以了解特定的子命令或概念。


## Git 概述

### Git 原理：

Git在每次提交更新时，都会浏览一遍所有文件的指纹信息(校验和)。如果发现哪个文件有变化，就对文件做一个快照，当到索引中；如果没有变化，就只对上次的快照做一个链接。
在保存到Git之前，所有数据都会进行SHA-1校验和计算，并将这个结果作为数据的索引和唯一标识。

### Git 工作区中文件的状态： 

- 未追踪 Untracked files
- 已更改、未暂存 Changes not staged for commit
- 已暂存、未提交 Changes to be committed
- 已提交 Committed

### 在 Git 中有三个区域：

- git directory : 本地仓库 repository
- staging area  : 暂存区 (index 索引)
- working directory : 工作区、工作树

### Git 特点：

1. 直接记录快照，而非差异比较  
2. 近乎所有操作都是本地执行  
3. 时刻保持数据完整性  checksum
4. 多数操作仅添加数据  
5. 文件的三种状态  


### Git 配置：

系统配置目录： /etc/gitconfig
当前用户配置目录： ~/.gitconfig
当前项目配置目录： ~/.git/config
```
git config --global user.name "my-name"
git config --global user.email "my email@email.com"
```
以上命令就是在你安装好 git 之后，告诉 git 你是谁，目前和 github 等托管代码的平台还没有半毛钱关系

### 关于分支

*使用分支其实就相当于在说：“我想基于这个提交以及它所有的父提交进行新的工作”*
git branch <branch-name>		创建分支
git checkout <branch-name>		切换分支
git checkout -b <branch-name>	创建并切换到分支
git merge bugfix				把`bugfix`合并到`master`(当前)分支
git rebase bugfix				把`bugfix`重定位到`master`(当前)分支

*在 Git 中合并两个分支时会产生一个特殊的提交记录，它有两个父节点。翻译成自然语言相当于：“我要把这两个父节点本身及它们所有的祖先都包含进来。”*

*第二种合并分支的方法是 git rebase。Rebase 实际上就是取出一系列的提交记录，“复制”它们，然后在另外一个地方逐个的放下去*。




### 基础命令
git init					新建一个git仓库
git pull					拉取
git pull origin master		拉取初始主分支
git status					查看目前的状态
git add <filename>			把文件从*工作区*添加到*暂存区*
git commit -m "comments"	把文件从*暂存区*提交到*仓库*
git log						查看提交的commit的信息
git remote add origin https://github.com/winjin/learngit.git	添加一个远程仓库的指针到本地
git push -u origin master	将本地的master分支推送到远程origin仓库	

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
5. git fetch   是把code从远程库获取到本地库中
6. git branch 分支名称(test) 新建分支(test)
- 切换分支
1. git checkout 分支名称(test)
- 合并分支
1. git checkout master 从test分支切换到master
2. git merge 子分支(test)

- 在master分支上面合并子分支
git branch --set-upstream-to=   添加上游

git reset --merge  恢复合并，回到merge之前的状态
git revert HEAD    恢复HEAD 
git reset HEAD~5   撤销过去最近的5个commit

- 删除分支
git branch -d [branch-name]
- 删除远程分支
git push origin --delete [branch-name]
git branch -dr [remote/branch]

++ rebase ： 所谓 rebase 就是你的提交历史的一个副本分支

## 基本流程

首先安装Git，然后最好配置如下：
```
git config --global user.name "my-name"
git config --global user.email "my email@email.com"
```
接下来，就是去建一个仓库，用git来管理了：

```
mkdir sandbox	-- 创建一个目录，用来作为一个仓库的目录
cd sandbox		-- 进入这个目录
git init		-- 初始化仓库 (这时起这个目录才真正算是一个git仓库)
touch README	-- 新建一个文件
git add README	-- 把文件添加到暂存区
git status		-- 查看文件的状态
git commit -m "first commit"	-- 把暂存区有修改的文件提交到本地仓库
git status
git diff
vim README  -- 修改文件内容
git add .
git add a.txt  
git rm a.txt
git add b.txt
git mv b.txt c.txt
git status
git diff --cached
git commit -m "update README file"
git revert e54c32785
git revert HEAD^

```

```
git diff <SHA1> 拿 Working Tree 比较
git diff <SHA1> <SHA1>
git diff --stat <SHA1>
git diff --cached 或 git diff --staged 拿 Staging Area 来比较

git clean -n 列出打算要清除的文档
git clean -f 真的清除
git clean -x 连 gitignore 里面的文档也清除掉

git branch -m old_name new_name 分支branch更名删除
git branch -M old_name new_name (强制覆盖)
git branch new_feature -d	分支branch删除
git branch new_feature -D (强制删除)

git stash  暂存起来
git stash apply
git stash clear
```


### Git 学习参考：
http://www.ruanyifeng.com/blog/2015/12/git-cheat-sheet.html
http://www.ruanyifeng.com/blog/2015/12/git-workflow.html



# Force Push Basics

If you’re working on a team and need to rebase a shared branch, here are the steps:

    Make sure your team has committed and pushed any pending changes
    Ask your team to pause work on that branch temporarily
    Make sure you have the latest changes for that branch (git pull)
    Rebase, then git push origin <yourbranch> -f
    Have your team fix up their local branches with git checkout <yourbranch>, git fetch and git reset --hard origin/<yourbranch>

