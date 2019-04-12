/**

 @Name：layuiAdmin 部门管理
 @Author：star1029
 @Site：http://www.layui.com/admin/
 @License：LPPL

 */

layui.define(['table', 'form', 'laydate'], function (exports) {
    var $ = layui.$
            , admin = layui.admin
            , admin_set = layui.setter.admin_set
            , laydate = layui.laydate
            , table = layui.table
            , form = layui.form
            , path = layui.router().path
            , type = path[path.length - 1]
            , table_name = type !== 'list' ? type : ''
            , table_id = 'LAY-trash-'+type+'-table'
    ;

    //数据列表
    table.render({
        elem: '#'+table_id
        , url: admin_set.domain + '/admin/trash/getData?table_name=' + table_name
        , cols: [[
            {type: 'checkbox'}
            , {field: 'id', title: '主键id', width: 80}
            , {field: 'title', title: '标题', minWidth: 120}
            , {field: 'table_name', title: '表名', minWidth: 120}
            , {field: 'content', title: '内容', minWidth: 300}
            , {field: 'create_time', title: '删除时间', width: 120}
            , {field: 'create_by', title: '操作者', width: 100}
            , {title: '操作', minWidth: 120, align: 'center', toolbar: '#'+table_id+'-option'}
        ]]
        , page: true
        , limit: admin_set.table_limit
        , limits: admin_set.table_limits
        , where: {
            access_token: layui.data(layui.setter.tableName).access_token
        }
        , text: { none: '暂无数据'}
    });

    //监听工具条
    table.on('tool(filter-trash-'+type+'-table)', function (obj) {
        var data = obj.data;
        if (obj.event === 'del') {
            layer.confirm('确定删除吗？', function (index) {
                admin.req({
                    url : '/admin/trash/delete'
                    ,data : {id:data.id}
                    ,done: function (res) {
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
        } else if (obj.event === 'recover') {
            layer.confirm('确定还原该笔数据吗？', function (index) {
                admin.req({
                    url: '/admin/trash/recover'
                    , data: {id: data.id}
                    , done: function (res) {
                        layer.close(index);
                        if (res.code) {
                            table.reload(table_id); //刷新表格
                            layer.msg(res.msg);
                        } else {
                            layer.alert(res.msg, {icon: 2});
                        }
                    }
                });
            });
        }
    });

    //搜索栏初始化。并赋值
    form.render(null, 'filter-trash-'+type);

    //日期事件，同时绑定多个
    lay('.trash-list-date').each(function () {
        laydate.render({
            elem: this
            , trigger: 'click'
        });
    });

    //监听搜索
    form.on('submit(filter-trash-'+type+'-search)', function (data) {
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
                id_list[key] = val.id;
            });

            layer.confirm('确定删除选中数据吗？', function (index) {
                admin.req({
                    url: '/admin/trash/delete'
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
        , batch_recover: function () {
            var checkStatus = table.checkStatus(table_id)
                    , checkData = checkStatus.data; //得到选中的数据

            if (checkData.length === 0) {
                return layer.msg('请选择数据');
            }

            //获取当前选中的id，数组
            var id_list = [];
            $.each(checkData, function (key, val) {
                id_list[key] = val.id;
            });

            layer.confirm('确定还原选中数据吗？', function (index) {
                admin.req({
                    url: '/admin/trash/recover'
                    , data: {id: id_list}
                    , done: function (res) {
                        layer.close(index);
                        if (res.code) {
                            table.reload(table_id); //刷新表格
                            layer.msg(res.msg);
                        } else {
                            layer.alert(res.msg, {icon: 2});
                        }
                    }
                });
            });
        }
    };

    $('#LAY-trash-'+type+'-btn .layui-btn').on('click', function () {
        var type = $(this).data('type');
        active[type] ? active[type].call(this) : '';
    });

    exports('trash', {})
});