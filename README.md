<p align="center">
    <img src="https://payjs.cn/static/images/logo.png" width=80 />
</p>
<h2 align="center">PayJs GateWay</h2>

## 简介

本项目是基于 PAYJS 的 API 开发，可直接用于生产环境

PAYJS 针对个人主体提供微信支付接入能力，是经过检验的正规、安全、可靠的微信支付个人开发接口

本项目适用于<a href="https://github.com/Tai7sy/card-system">Tai7sy/card-system</a>

## 使用方法

1、在app\Library\Pay 新建文件夹，可以任意命名（这里假设为PayjsWX）。

2、将本项目上传至PayjsWX内。

3、进入后台，新建支付渠道，“名称”自定、“图片”自定、“方式”填写“NATIVE”、“费率”自定、“驱动”填写PayjsWX、JSON配置： 

{
  "mchid": "你的商户ID", 
  "key": "你的商户KEY"
}

## 当前版本
version 1.0

## 意见与建议
如果发现bug或有优化方案，请submit issues，如果觉得好用，不妨来个star
