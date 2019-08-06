# JS判断一个变量是否存在于一个数组中



## 方法一：indexOf()
```
function IsInArray(arr,val){ 
	return arr.indexOf(val) !== -1;
} 
var arr=[1,2,3];
IsInArray(arr,3);	// true
```

注：IE8以下的Array并没有indexOf这个方法，解决办法，给Array的原型添加indexOf方法。把以下代码放在你的indexOf方法的上面：

```
if(!Array.prototype.indexOf){
	Array.prototype.indexOf = function(elt){
		var len = this.length >>> 0;
		var from = Number(arguments[1]) || 0;
		from = (from < 0) ? Math.ceil(from) : Math.floor(from);
		if(from < 0)
			from += len;
		for(; from < len; from++) {
			if(from in this && this[from] === elt){
				return from;
			}	
		}
		return -1;
	};
}
```

## 方法二：正则表达式

```
Array.prototype.in_array=function(e){
	var r=new RegExp(','+e+',');
	return r.test(','+this.join(this.S)+',');
};

var arr = [1, 2, 3];
arr.in_array(3);//true

```
注：此函数只对字符和数字有效。


## 方法三：arr.find() 

```
function IsInArray(arr, val) {
	var rel=arr.find((v) => {
		return v == val
	});
	return rel?true:false; 
}
var arr = [1, 2, 3];
IsInArray(arr,3);//true

```
find()函数用来查找目标元素，找到就返回该元素，找不到返回undefined，  是es6里的。除了上面形式，还可以通过arr.find(callback) 在回调中判断。
```
function IsInArray(arr, val,callback) {
	arr.find(v => {
	    if (v === val){
	      callback()
	　     }
	})
}
var arr = [1, 2, 3];
IsInArray(arr,3,()=>{
	console.log("arr数组中包含了3")
});
// arr数组中包含了3
```

## 方法四：for循环结合if判断

```
Array.prototype.in_array = function (val) { 
　　for (var i = 0; i < this.length; i++) { 
　　	if (this[i] == val) { 
　　		return true; 
    } 
  } 
  return false;
} 
var arr = [1, 2, 3];
arr.in_array(3)//true
```

该方法兼容性较好，也是平时开发中最常用的方式。