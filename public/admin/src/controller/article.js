/**
 @Name：layuiAdmin 用户管理
 @Author：star1029
 @Site：http://www.layui.com/admin/
 @License：LPPL
 */

layui.define(['table', 'form'], function (exports) {
    var $ = layui.$
            , admin = layui.admin
            , admin_set = layui.setter.admin_set
            , table = layui.table
            , form = layui.form
            , path = layui.router().path
            , type = path[path.length - 1]
            , table_id = 'LAY-article-'+type+'-table'
    ;

    //数据列表
    table.render({
        elem: '#'+table_id
        , url: admin_set.domain + '/admin/article/getData?cat_type_code=' + type
        , cols: [[
            {type: 'checkbox'}
            , {field: 'cat_name', title: '栏目', minWidth: 80}
            , {
                title: '主图', width: '10%', align: 'center', templet: function (data) {
                    return data.img_url !== '' ? '<a href="' + data.img_url + '" target="_blank"><img src="' + data.img_url + '?imageView2/2/h/35" alt="" style="height: 35px;"></a>' : "";
                }
            }
            , {field: 'title', title: '标题', minWidth: 100}
            , {field: 'level_text', title: '推荐等级', minWidth: 80}
            , {field: 'read_num', title: '阅读次数', minWidth: 80}
            , {
                title: '更改排序', align: 'center', minWidth: 90, templet: function (data) {
                    return '<div class="layui-btn-group">' +
                            '<button type="button" class="layui-btn layui-btn-primary layui-btn-xs change_sort" data-type="asc" data-id="' + data.art_id + '"><i class="layui-icon layui-icon-up"></i></button>' +
                            '<button type="button" class="layui-btn layui-btn-primary layui-btn-xs change_sort" data-type="desc" data-id="' + data.art_id + '"><i class="layui-icon layui-icon-down"></i></button>' +
                            '</div>';
                }
            }
            , {
                title: '是否发布', width: 90, align: 'center', templet: function (data) {
                    var check_state = data.state == 1 ? 'checked' : '';
                    return '<input type="checkbox" name="state" value="' + data.state + '" data-id="' + data.art_id + '" lay-skin="switch"  lay-text="是|否" lay-filter="statusEvent" ' + check_state + '>';
                }
            }
            , {title: '操作', minWidth: 120, align: 'center', toolbar: '#'+table_id+'-option'}
        ]]
        , page: true
        , limit: admin_set.table_limit
        , limits: admin_set.table_limits
        , where: {
            access_token: layui.data(layui.setter.tableName).access_token
        }
        , text: { none: '暂无数据'}
        , done: function (res, curr, count) {
            //改变排序
            $(".change_sort").ajaxSort('/admin/article/changeSort');
        }
    });

    //监听工具条
    table.on('tool(filter-article-'+type+'-table)', function (obj) {
        var data = obj.data;
        if (obj.event === 'del') {
            layer.confirm('确定删除吗？', function (index) {
                admin.req({
                    url: '/admin/article/delete'
                    , data: {id: data.art_id}
                    , done: function (res) {
                        layer.close(index);
                        if (res.code) {
                            obj.del();
                            layer.msg(res.msg);
                        } else {
                            layer.alert(res.msg, {icon: 2});
                        }
                    }
                });
            });
        } else if (obj.event === 'edit') {
            location.hash = '/article/edit/art_id=' + data.art_id;
        }
    });

    form.render(null, 'filter-article-'+type);

    //监听搜索
    form.on('submit(filter-article-'+type+'-search)', function (data) {
        var field = data.field;
        //执行重载
        table.reload(table_id, {
            where: field
            ,page:{
                curr: 1 //重新从第 1 页开始
            }
        });
        return false;
    });

    //事件
    var active = {
        batchdel: function () {
            var checkStatus = table.checkStatus(table_id)
                    , checkData = checkStatus.data; //得到选中的数据

            if (checkData.length === 0) {
                return layer.msg('请选择数据');
            }

            //获取当前选中的id，数组
            var id_list = [];
            $.each(checkData, function (key, val) {
                id_list[key] = val.art_id;
            });

            layer.confirm('确定删除选中数据吗？', function (index) {
                admin.req({
                    url: '/admin/article/delete'
                    , data: {id: id_list}
                    , done: function (res) {
                        layer.close(index);
                        if (res.code) {
                            table.reload(table_id);
                            layer.msg(res.msg);
                        } else {
                            layer.alert(res.msg, {icon: 2});
                        }
                    }
                });
            });
        }
        , add: function () {
            location.hash = '/article/edit';
        }
    };

    $('#LAY-article-'+type+'-btn .layui-btn').on('click', function () {
        var type = $(this).data('type');
        active[type] ? active[type].call(this) : '';
    });

    exports('article', {})
});