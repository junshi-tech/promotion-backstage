/**

 @Name：layuiAdmin 用户管理 管理员管理 角色管理
 @Author：star1029
 @Site：http://www.layui.com/admin/
 @License：LPPL

 */

layui.define(['table', 'form'], function (exports) {
    var $ = layui.$
        , admin = layui.admin
        , admin_set = layui.setter.admin_set
        , table = layui.table
        , form = layui.form;

    //数据列表
    table.render({
        elem: '#LAY-admin-role-list-table'
        , url: admin_set.domain+'/admin/admin_role/getData'
        , cols: [[
            {type: 'checkbox'}
            , {field: 'role_name', title: '权限组名称', minWidth: 80}
            , {field: 'describe', title: '描述', minWidth: 400}
            ,{title:'更改排序', align:'center', minWidth: 90, templet: function (data) {
                return '<div class="layui-btn-group">' +
                        '<button type="button" class="layui-btn layui-btn-primary layui-btn-xs" lay-event="change_sort" data-type="asc"><i class="layui-icon layui-icon-up"></i></button>' +
                        '<button type="button" class="layui-btn layui-btn-primary layui-btn-xs" lay-event="change_sort" data-type="desc"><i class="layui-icon layui-icon-down"></i></button>' +
                        '</div>';
            }}
            , {field: 'create_time', title: '添加日期', minWidth: 100}
            , {title: '操作', minWidth: 120, align: 'center', toolbar: '#LAY-admin-role-list-table-option'}
        ]]
        , page: true
        , limit: admin_set.table_limit
        , limits: admin_set.table_limits
        ,where: {
            access_token: layui.data(layui.setter.tableName).access_token
        }
        , text: { none: '暂无数据'}
    });

    //监听工具条
    table.on('tool(filter-admin-role-list-table)', function (obj) {
        var data = obj.data;
        if (obj.event === 'del') {
            layer.confirm('确定删除吗？', function(index){
                admin.req({
                    url : '/admin/admin_role/delete'
                    ,data : {id:data.role_id}
                    ,done: function (res) {
                        layer.close(index);
                        if (res.code) {
                            obj.del();
                            layer.msg(res.msg);
                        } else {
                            layer.alert(res.msg, {icon:2});
                        }
                    }
                });
            });
        } else if (obj.event === 'edit') {
            location.hash = '/admin_role/edit/role_id='+data.role_id;
        } else if (obj.event === 'change_sort') {
            var data_attr = $(this).data();
            admin.req({
                url : '/admin/admin_role/changeSort'
                ,data : {id:data.role_id,'type':data_attr.type}
                ,done: function (res) {
                    if (res.code) {
                        table.reload('LAY-admin-role-list-table');
                        layer.msg('更改成功！');
                    } else {
                        layer.alert(res.msg, {icon:2});
                    }
                }
            });
        }
    });

    //搜索栏初始化。并赋值
    form.render(null, 'filter-admin-role-list');

    //监听搜索
    form.on('submit(filter-admin-role-list-search)', function (data) {
        var field = data.field;
        //执行重载
        table.reload('LAY-admin-role-list-table', {
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
            var checkStatus = table.checkStatus('LAY-admin-role-list-table')
                    , checkData = checkStatus.data; //得到选中的数据

            if (checkData.length === 0) {
                return layer.msg('请选择数据');
            }

            //获取当前选中的id，数组
            var id_list = [];
            $.each(checkData, function (key, val) {
                id_list[key] = val.role_id;
            });

            layer.confirm('确定删除吗？', function(index){
                admin.req({
                    url : '/admin/admin_role/delete'
                    ,data : {id:id_list}
                    ,done: function (res) {
                        layer.close(index);
                        if (res.code) {
                            table.reload('LAY-admin-role-list-table');
                            layer.msg(res.msg);
                        } else {
                            layer.alert(res.msg, {icon:2});
                        }
                    }
                });
            });
        }
        , add: function () {
            location.hash = '/admin/role_edit';
        }
    };

    $('#LAY-admin-role-list-btn .layui-btn').on('click', function () {
        var type = $(this).data('type');
        active[type] ? active[type].call(this) : '';
    });

    exports('admin_role', {})
});