## css 总结

![css priority](https://github.com/winjin/learngit/blob/master/priority_css.jpg)
权重主要分为 4 个等级：

- 第一等：代表内联样式，如: style=""，权值为1000
- 第二等：代表ID选择器，如：#content，权值为100
- 第三等：代表类，伪类和属性选择器，如.content，权值为10
- 第四等：代表类型选择器和伪元素选择器，如div p，权值为1

+ 优先级遵循如下法则：
+ 选择器都有一个权值，权值越大越优先；
+ 当权值相等时，*后*出现的样式表设置要**优于***先*出现的样式表设置；
+ 创作者的规则高于浏览者：即网页编写者设置的 CSS 样式的优先权高于浏览器所设置的样式；Developer &gt; Viewer User
+ 继承的 CSS 样式不如后来指定的 CSS 样式；
+ 在同一组属性设置中标有_!important_规则的优先级最大