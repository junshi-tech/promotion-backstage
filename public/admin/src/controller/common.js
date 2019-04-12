/**

 @Name：layuiAdmin 公共业务
 @Author：十万马
 @Site：http://www.layui.com/admin/
 @License：LPPL

 */

layui.define(function (exports) {
    var $ = layui.$
            , layer = layui.layer
            , setter = layui.setter
            , admin = layui.admin;

    /**
     * 退出
     */
    admin.events.logout = function () {
        admin.req({
            url: '/admin/login/loginOut'
            , done: function (res) {
                //清空本地记录的 token，并跳转到登入页
                admin.exit();
            }
        });
    };

    /**
     * 设置图片路径
     */
    $.fn.extend({
        setImgVal: function (url) {
            var dom = $(this).closest('.layui-form-item');
            dom.find('a').attr('href', url);//图片链接
            dom.find('img').attr('src', url);//图片链接
            dom.find('input[type="hidden"]').val(url);//赋值上传
        }
    });

    /**
     * 图片上传
     */
    $.fn.extend({
        imgUpload: function () {
            var dom = $(this);
            layui.use('upload', function () {
                var upload = layui.upload;
                var lay_load;
                upload.render({
                    elem: dom.selector
                    ,url: setter.admin_set.upload_url
                    ,size: setter.admin_set.upload_size //限制文件大小，单位 KB
                    ,exts: "jpg|png|gif|bmp|jpeg"
                    ,before: function(obj){
                        //预读本地文件示例，不支持ie8
                        obj.preview(function(index, file, result){
                            dom.closest('.layui-form-item').find('img').attr('src', result);//图片链接（base64）
                        });
                        lay_load = layer.load(2, {time: 20*1000});
                    }
                    ,done: function(res){
                        layer.close(lay_load);
                        if(res.code){
                            dom.setImgVal(res.data);
                        } else {
                            layer.alert(res.msg);
                        }
                    }
                });
            });

        }
    });
    /**
     * 删除图片
     */
    $.fn.extend({
        imgDel: function (id, table_name) {
            $(this).click(function() {
                var dom = $(this).closest('.layui-form-item').find('input[type="hidden"]');
                if (dom.val() !== '') {
                    layer.confirm('删除后无法恢复，确定继续吗？', function(index){
                        if (id > 0) {
                            admin.req({
                                url: '/admin/upload_file/deleteImg'
                                ,data: {id:id, table_name:table_name, field_name:dom.attr("name"), img_url:dom.val()}
                                ,done: function (res) {
                                    layer.close(index);
                                    if (res.code) {
                                        dom.setImgVal('');
                                    } else {
                                        layer.alert(res.msg, {icon:2});
                                    }
                                }
                            });
                        } else {
                            layer.close(index);
                            dom.setImgVal('');
                        }
                    });
                }
            });
        }
    });

    /**
     * 删除图片
     */
    $.fn.extend({
        imgDelQiNiu: function (id, table_name) {
            $(this).click(function() {
                var dom = $(this).closest('.layui-form-item').find('input[type="hidden"]');
                if (dom.val() !== '') {
                    layer.confirm('删除后无法恢复，确定继续吗？', function(index){
                        if (id > 0) {
                            admin.req({
                                url: '/admin/Qiniu/deleteFile'
                                ,data: {id:id, table_name:table_name, field_name:dom.attr("name"), img_url:dom.val()}
                                ,done: function (res) {
                                    layer.close(index);
                                    if (res.code) {
                                        dom.setImgVal('');
                                    } else {
                                        layer.alert(res.msg, {icon:2});
                                    }
                                }
                            });
                        } else {
                            layer.close(index);
                            dom.setImgVal('');
                        }
                    });
                }
            });
        }
    });

    /**
     * 更改顺序
     */
    $.fn.extend({
        ajaxSort: function (url) {
            $(this).click(function() {
                var dom = $(this);
                admin.req({
                    url : url
                    ,data : {id:dom.data('id'),'type':dom.data('type')}
                    ,done: function (res) {
                        if (res.code) {
                            var table_id = dom.closest('.layui-table-view').attr('lay-id');
                            layui.table.reload(table_id);
                            layer.msg('更改成功！');
                        } else {
                            layer.alert(res.msg, {icon:2});
                        }
                    }
                });
            });
        }
    });

    /**
     * 颜色选择器
     */
    $.fn.extend({
        colorPicker: function () {
            var selector = $(this).selector;
            layui.use('colorpicker', function () {
                var colorpicker = layui.colorpicker;
                colorpicker.render({
                    elem: selector
                    , predefine: true  // 开启预定义颜色
                    , done: function (color) {
                        color || this.change(color); //清空时执行 change
                    }
                    , change: function (color) {
                        //给当前内容设置颜色
                        $(this.elem).closest('.layui-form-item').find('input[type="text"]').attr('style', 'color: ' + color);
                    }
                });
            });

        }
    });

    /**
     * 自动将form表单封装成json对象
     */
    $.fn.serializeObject = function () {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function () {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };

    //对外暴露的接口
    exports('common', {});
});