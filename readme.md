# NGender PHP

这是[https://github.com/observerss/ngender](https://github.com/observerss/ngender)的PHP版本.

## Usage

复制两个文件 (ngender.php & charfreq.csv) 到一个支持PHP的网站.
用HTTP/GET请求网站, 参数是"name=xxx", 网站内容是json格式, 可以被其它程序解析.
可选参数：
1. keepFirst=true   不跳过第一个字（姓） 一般在查询非人名时用到（如网名）

## Example

```
http://localhost/ngender.php?name=赵本山
{"result": ["male", 0.9836229687547], "keepFirst": false}

http://localhost/ngender.php?name=宋丹丹
{"result": ["female", 0.97594861289499], "keepFirst": false}
```

## About

### BiliBili

https://space.bilibili.com/15858903