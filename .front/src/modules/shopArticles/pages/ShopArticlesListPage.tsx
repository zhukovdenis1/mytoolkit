import React, { useEffect, useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { Table, Input, Select, Button, Form, Space, message, Confirmable, ButtonLink } from "ui";
import type { TablePaginationConfig, ColumnsType } from "@ui/types";
import { EditOutlined, DeleteOutlined } from '@ui/icons';
import { api, route } from "api";

interface Params {
    search?: string;
    site_id?: string;
    product_id?: string;
    _page?: number;
    _limit?: number;
    _sort?: string;
    _order?: string;
}

export const ShopArticlesListPage: React.FC = () => {

    const [data, setData] = useState([]);
    const [siteList, setSiteList] = useState<{ id: number; name: string }[]>([]);
    const [loading, setLoading] = useState(false);
    const [pagination, setPagination] = useState<TablePaginationConfig>({
        current: 1,
        pageSize: 5,
        total: 0,
        showSizeChanger: true,
    });
    const [sorter, setSorter] = useState<{ field?: string; order?: string }>({});
    const [form] = Form.useForm();
    const navigate = useNavigate();

    const handleTableChange = (pag: TablePaginationConfig, _: any, sorterObj: any) => {
        const newSorter = sorterObj.order
            ? { field: sorterObj.field, order: sorterObj.order === "ascend" ? "asc" : "desc" }
            : {};

        setPagination(pag);
        setSorter(newSorter);
        form.submit();
    };

    const columns: ColumnsType = [
        { title: "ID", dataIndex: "id", key: "id", sorter: true, width: 50 },
        {
            title: "Name",
            dataIndex: "name",
            key: "name",
            sorter: true,
            render: (_, record) => (
                <a href={`https://deshevyi.ru/a-${record.id}`} target="_blank">{record.name}</a>
            ),
        },
        {
            title: "s_id",
            dataIndex: "site_id",
            key: "site_id",
            sorter: true
        },
        {
            title: "p_id",
            dataIndex: "product_id",
            key: "product_id",
            sorter: true
        },
        {
            title: "Actions",
            key: "action",
            fixed: "right",
            width: 50,
            render: (_, record) => (
                <Space>
                    <Link to={route('shop.articles.edit', {"article_id": record.id})}><EditOutlined /></Link>
                    <Confirmable onConfirm={() => deleteArticle(record.id)}>
                        <Button
                            type="link"
                            title="delete"
                            icon={<DeleteOutlined />}
                        />
                    </Confirmable>
                </Space>
            ),
        },
    ];

    useEffect(() => {
        const params = Object.fromEntries(new URLSearchParams(location.search).entries());
        form.setFieldsValue({
            search: params.search
        });
        setPagination((prev) => ({
            ...prev,
            current: Number(params._page) || 1,
            pageSize: Number(params._limit) || 5,
            total: Number(params._total) || 0,
        }));
        fetchData(params);

    }, [location.search]);

    const fetchData = async (params: Params = {}) => {
        setLoading(true);

        const response = await api.safeRequestWithAlert("admin.shop.articles.list", params);
        if (response.success) {
            setData(response.data.articles);
            setPagination((prev) => ({
                ...prev,
                current: response.data.meta.current_page,
                pageSize: response.data.meta.per_page,
                total: response.data.meta.total,
            }));
        }

        const siteListResponse = await api.safeRequestWithAlert("admin.shop.siteList", {"group": "shop"});
        if (siteListResponse.success) {
            setSiteList(siteListResponse.data.data);
        }

        setLoading(false);
    };

    const reload = () => {
        form.submit();
    };

    const deleteArticle = async (articleId: number) => {
        const response = await api.safeRequestWithAlert(`admin.shop.articles.delete`, { article_id: articleId });
        if (response.success) {
            reload();
            message.success('Article was deleted successfully');
        }
    };

    const updateUrl = (params: Params) => {
        const queryString = new URLSearchParams(params as Record<string, string>).toString();
        navigate(`?${queryString}`);
    };

    const handleSearch = (values: any) => {
        const toPage = pagination.current ?? 1;
        const fromPage = new URLSearchParams(window.location.search).get('_page') ?? 1;
        //console.log(`${toPage}=${fromPage}`)
        const queryParams: Params = {
            search: values.search || "",
            site_id: values.site_id || "",
            product_id: values.product_id || "",
            _page: (toPage == fromPage) ? 1 : toPage,//сбрасыаем на 1, если преход не по страницам, а => меняются условия поиска
            _limit: pagination.pageSize,
            _sort: sorter.field || "",
            _order: sorter.order || "",
        };

        updateUrl(queryParams);
        //fetchData(queryParams);
    };

    return (
        <>
            <Form form={form} layout="inline" onFinish={handleSearch} className="line">
                <Form.Item name="search">
                    <Input placeholder="Search..." autoComplete="off" />
                </Form.Item>
                <Form.Item name="product_id">
                    <Input placeholder="Product ID..." autoComplete="off" />
                </Form.Item>
                <Form.Item name="site_id">
                    <Select
                        placeholder="Select site"
                        options={siteList.map(item => ({ value: item.id, label: item.name }))}
                    />
                </Form.Item>
                <Form.Item>
                    <Space>
                        <Button type="primary" htmlType="submit">Search</Button>
                        <Button htmlType="button" onClick={() => navigate(route('shop.articles'))}>Reset</Button>
                    </Space>
                </Form.Item>
            </Form>
            <div className="button-box">
                <ButtonLink type="primary2" to={route('shop.articles.add')}>Add</ButtonLink>
            </div>
            <Table
                columns={columns}
                dataSource={data}
                rowKey="id"
                loading={loading}
                pagination={{
                    ...pagination,
                    showSizeChanger: true,
                    pageSizeOptions: ["5", "10", "20", "50"],
                }}
                onChange={handleTableChange}
                scroll={{ x: "max-content" }}
            />
        </>
    );
};

