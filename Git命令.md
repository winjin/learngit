# Git 命令

## 安装 Git ，提交用户名和邮箱地址

```
git config --global user.name "my-name"
git config --global user.email "my email@email.com"
```
以上命令就是在你安装好git之后，告诉git你是谁，目前和github等托管代码的平台还没有半毛钱关系

## 基础命令

git init					新建一个git仓库
git pull					拉取
git pull origin master		拉取初始主分支
git status					查看目前的状态
git add <filename>			把文件从*工作区*添加到*暂存区*
git commit -m "comments"	把文件从*暂存区*提交到*仓库*
git log						查看提交的commit的信息
git remote add origin https://github.com/winjin/learngit.git	添加一个远程仓库的指针到本地
git push -u origin master	将本地的master分支推送到远程origin仓库	


## 关于分支

*使用分支其实就相当于在说：“我想基于这个提交以及它所有的父提交进行新的工作”*。

git branch <branch-name>		创建分支
git checkout <branch-name>		切换分支
git checkout -b <branch-name>	创建并切换到分支
git merge bugfix				把`bugfix`合并到`master`(当前)分支
git rebase bugfix				把`bugfix`重定位到`master`(当前)分支

*在 Git 中合并两个分支时会产生一个特殊的提交记录，它有两个父节点。翻译成自然语言相当于：“我要把这两个父节点本身及它们所有的祖先都包含进来。”*

*第二种合并分支的方法是 git rebase。Rebase 实际上就是取出一系列的提交记录，“复制”它们，然后在另外一个地方逐个的放下去*。



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
Git 有三个区域：

- 工作区 working directory
- 暂存区 staging area (Index)
- 仓库区 repository

工作区里的文件有四中状态：
未追踪 Untracked files
已更改、未暂存 Changes not staged for commit
已暂存、未提交 Changes to be committed
已提交 Committed

```
git diff <SHA1> 拿 Working Tree 比較
git diff <SHA1> <SHA1>
git diff --stat <SHA1>
git diff --cached 或 git diff --staged 拿 Staging Area 來比較

git clean -n 列出打算要清除的檔案
git clean -f 真的清除
git clean -x 連 gitignore 裡列的檔案也清掉

git branch -m old_name new_name 分支branch更名删除
git branch -M old_name new_name (強制覆蓋)
git branch new_feature -d	分支branch删除
git branch new_feature -D (強制刪除)

git stash  暂存起来
git stash apply
git stash clear
```




























