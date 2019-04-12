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
        elem: '#LAY-banner-list-table'
        , url: admin_set.domain+'/admin/banner/getData'
        , cols: [[
            {type: 'checkbox'}
            , {field: 'location', title: '位置', minWidth: 120}
            ,{title:'图片' , minWidth: 150, align:'center', templet: function (data) {
                return data.img_url_full != '' ? '<a href="'+data.img_url_full+'" target="_blank"><img src="'+data.img_url_full+'" alt="" style="height: 35px;"></a>' : "";
            }}
            , {field: 'img_name', title: '名称', minWidth: 120}
            , {field: 'link', title: '跳转链接', minWidth: 120, templet: function (data) {
                return data.link != '' ? '<a href="'+data.link+'" target="_blank">'+data.link+'</a>' : "";
            }}
            , {field: 'state_text', title: '是否显示', minWidth: 95, align:'center'}
            , {field: 'create_time', title: '添加日期', minWidth: 90}
            , {title: '操作', minWidth: 120, align: 'center', toolbar: '#LAY-banner-list-table-option'}
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
    table.on('tool(filter-banner-list-table)', function (obj) {
        var data = obj.data;
        if (obj.event === 'del') {
            layer.confirm('确定删除吗？', function(index){
                admin.req({
                    url: '/admin/banner/delete'
                    , data: {id: data.banner_id}
                    , done: function (res) {
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
            location.hash = '/banner/edit/banner_id='+data.banner_id;
        }
    });

    //搜索栏初始化。并赋值
    form.render(null, 'filter-banner-list');

    //监听搜索
    form.on('submit(filter-banner-list-search)', function (data) {
        var field = data.field;
        //执行重载
        table.reload('LAY-banner-list-table', {
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
            var checkStatus = table.checkStatus('LAY-banner-list-table')
                    , checkData = checkStatus.data; //得到选中的数据

            if (checkData.length === 0) {
                return layer.msg('请选择数据');
            }

            //获取当前选中的id，数组
            var id_list = [];
            $.each(checkData, function (key, val) {
                id_list[key] = val.banner_id;
            });

            layer.confirm('确定删除吗？', function(index){
                admin.req({
                    url : '/admin/banner/delete'
                    ,data : {id:id_list}
                    ,done: function (res) {
                        layer.close(index);
                        if (res.code) {
                            table.reload('LAY-banner-list-table');
                            layer.msg(res.msg);
                        } else {
                            layer.alert(res.msg, {icon:2});
                        }
                    }
                });
            });
        }
        , add: function () {
            location.hash = '/banner/edit';
        }
    };

    $('#LAY-banner-list-btn .layui-btn').on('click', function () {
        var type = $(this).data('type');
        active[type] ? active[type].call(this) : '';
    });

    exports('banner', {})
});