/**

 @Name：layuiAdmin 部门管理
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
            , table_id = 'LAY-article-cat-list-table'
    ;

    //数据列表
    table.render({
        elem: '#' + table_id
        , url: admin_set.domain + '/admin/article_cat/getData'
        , cols: [[
            {type: 'checkbox'}
            , {field:'cat_type_text', title:'所属板块', minWidth: 80}
            , {field: 'cat_name', title: '栏目名称', minWidth: 80}
            , {field:'location_text', title:'显示位置'}
            , {
                title: '更改排序', align: 'center', minWidth: 90, templet: function (data) {
                    return '<div class="layui-btn-group">' +
                            '<button type="button" class="layui-btn layui-btn-primary layui-btn-xs change_sort" data-type="asc" data-id="' + data.cat_id + '"><i class="layui-icon layui-icon-up"></i></button>' +
                            '<button type="button" class="layui-btn layui-btn-primary layui-btn-xs change_sort" data-type="desc" data-id="' + data.cat_id + '"><i class="layui-icon layui-icon-down"></i></button>' +
                            '</div>';
                }
            }
            , {field: 'create_time', title: '添加日期', minWidth: 100}
            , {title: '操作', minWidth: 120, align: 'center', toolbar: '#' + table_id + '-option'}
        ]]
        , page: true
        , limit: admin_set.table_limit
        , limits: admin_set.table_limits
        , where: {
            access_token: layui.data(layui.setter.tableName).access_token
        }
        , text: { none: '暂无数据'}
        , done: function (res, curr, count) {
            $(".change_sort").ajaxSort('/admin/article_cat/changeSort');//改变排序
        }
    });

    //监听工具条
    table.on('tool(filter-article-cat-list-table)', function (obj) {
        var data = obj.data;
        if (obj.event === 'del') {
            layer.confirm('确定删除吗？', function (index) {
                admin.req({
                    url: '/admin/article_cat/delete'
                    , data: {id: data.cat_id}
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
            location.hash = '/article/cat_edit/cat_id=' + data.cat_id;
        }
    });

    //搜索栏初始化。并赋值
    form.render(null, 'filter-article-cat-list');

    //监听搜索
    form.on('submit(filter-article-cat-list-search)', function (data) {
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
                id_list[key] = val.cat_id;
            });

            layer.confirm('确定删除吗？', function (index) {
                admin.req({
                    url: '/admin/article_cat/delete'
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
            location.hash = '/article/cat_edit';
        }
    };

    $('#LAY-article-cat-list-btn .layui-btn').on('click', function () {
        var type = $(this).data('type');
        active[type] ? active[type].call(this) : '';
    });

    exports('article_cat', {})
});