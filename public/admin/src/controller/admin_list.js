/**

 @Name：layuiAdmin 用户管理
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
        , form = layui.form;

    //数据列表
    table.render({
        elem: '#LAY-admin-list-table'
        , url: admin_set.domain+'/admin/admin/getData'
        , cols: [[
            {type: 'checkbox'}
            , {field: 'user_name', title: '用户名', minWidth: 80}
            , {field: 'tel', title: '手机号', minWidth: 100}
            , {field: 'role_text', title: '所属权限分组', minWidth: 80}
            , {field: 'dept_text', title: '部门', minWidth: 80}
            , {field: 'describe', title: '个性签名', minWidth: 250}
            , {field: 'create_time', title: '注册日期', minWidth: 100}
            , {title: '操作', minWidth: 120, align: 'center', toolbar: '#LAY-admin-list-table-option'}
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
    table.on('tool(filter-admin-list-table)', function (obj) {
        var data = obj.data;
        if (obj.event === 'del') {
            layer.confirm('确定删除吗？', function(index){
                admin.req({
                    url : '/admin/admin/delete'
                    ,data : {id:data.admin_id}
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
            location.hash = '/admin/edit/admin_id='+data.admin_id;
        }
    });

    form.render(null, 'filter-admin-list');

    //日期事件，同时绑定多个
    lay('.admin-list-date').each(function(){
        laydate.render({
            elem: this
            ,trigger: 'click'
        });
    });

    //监听搜索
    form.on('submit(filter-admin-list-search)', function (data) {
        var field = data.field;
        //执行重载
        table.reload('LAY-admin-list-table', {
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
            var checkStatus = table.checkStatus('LAY-admin-list-table')
                    , checkData = checkStatus.data; //得到选中的数据

            if (checkData.length === 0) {
                return layer.msg('请选择数据');
            }

            //获取当前选中的id，数组
            var id_list = [];
            $.each(checkData, function (key, val) {
                id_list[key] = val.admin_id;
            });

            layer.confirm('确定删除吗？', function(index){
                admin.req({
                    url : '/admin/admin/delete'
                    ,data : {id:id_list}
                    ,done: function (res) {
                        layer.close(index);
                        if (res.code) {
                            table.reload('LAY-admin-list-table');
                            layer.msg(res.msg);
                        } else {
                            layer.alert(res.msg, {icon:2});
                        }
                    }
                });
            });
        }
        , add: function () {
            location.hash = '/admin/edit';
        }
    };

    $('#LAY-admin-list-btn .layui-btn').on('click', function () {
        var type = $(this).data('type');
        active[type] ? active[type].call(this) : '';
    });

    exports('admin_list', {})
});