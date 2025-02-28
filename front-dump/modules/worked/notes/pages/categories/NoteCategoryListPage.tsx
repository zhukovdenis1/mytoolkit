import React, {useEffect, useState} from 'react';
import { Link } from "react-router-dom";
import { DownOutlined } from '@ui/icons';
import {Input, Tree, Space, showModal, Spin, Button, Confirmable, message } from 'ui';
import type {TreeDataNode, TreeProps } from '@ui/types';
import {api, route} from "api";
import {NoteCategoryFormPage} from "@/modules/notes/pages/categories/NoteCategoryFormPage";

export const NoteCategoryListPage: React.FC = () => {
    const [search, setSearch] = useState('');
    const [originalTreeData, setOriginalTreeData] = useState([]);
    const [treeData, setTreeData] = useState([]);
    const [expandedKeys, setExpandedKeys] = useState<string[]>([]);
    const [loading, setLoading] = useState(true)
    const [reloadTrigger, setReloadTrigger] = useState(0);

    const fetchCategories = async () => {
        const response = await api.safeRequest("notes.categories.tree");
        const data = formatTreeData(response.data.data || [])
        setOriginalTreeData(data);
        //setTreeData(data);
        searchInTree('', data)
        setLoading(false)
    };

    useEffect(() => {
        setLoading(true)
        fetchCategories();
    }, [reloadTrigger])

    const onSearch = (e: React.ChangeEvent<HTMLInputElement>) => {
        const value = e.target.value;
        searchInTree(value, originalTreeData)
    };

    const searchInTree = (value: string, treeData) => {
        setSearch(value);
        const [filteredData, keys] = filterTree(treeData, value);
        setTreeData(filteredData);
        setExpandedKeys(keys);
    }

    const filterTree = (data: TreeDataNode[], search: string): [TreeDataNode[], string[]] => {
        let expandedKeys: string[] = [];

        const filterNode = (node: TreeDataNode): TreeDataNode | null => {
            const highlightedTitle = highlightText(node, search);

            if (node.children) {
                const filteredChildren = node.children.map(filterNode).filter(Boolean) as TreeDataNode[];
                if (filteredChildren.length > 0 || node.title.toLowerCase().includes(search.toLowerCase())) {
                    expandedKeys.push(node.key.toString());
                    //return { ...node, children: filteredChildren, title: highlightText(node, search) };
                    return {
                        ...node,
                        children: filteredChildren,
                        title: nodeHtml(node.key as string, highlightedTitle as string, () => setReloadTrigger(prev => prev + 1))
                    };
                }
            }
            if (node.title.toLowerCase().includes(search.toLowerCase())) {
                return {
                    ...node,
                    title: nodeHtml(node.key as string, highlightedTitle as string, () => setReloadTrigger(prev => prev + 1))
                };
            }

            return null;
        };

        return [data.map(filterNode).filter(Boolean) as TreeDataNode[], expandedKeys];
    };

    return (
        <Spin spinning={loading}>
            <Input placeholder="Search" value={search} onChange={onSearch} style={{ marginBottom: 8 }} />
            <Tree
                showLine
                switcherIcon={<DownOutlined />}
                expandedKeys={expandedKeys}
                onExpand={setExpandedKeys}
                treeData={treeData}
                // treeData={treeData.map(node => ({//только ради reload усложнено
                //     ...node,
                //     title: nodeHtml(node.key as string, node.title as string, () => setReloadTrigger(prev => prev + 1))
                // }))}
            />
            <Button type="primary" htmlType="button" onClick={() => showModal(<NoteCategoryFormPage />,  {
                title: "Add category",
                loading: true,
                onClose: (data) => {
                    if (data?.reload) {
                        setReloadTrigger(prev => prev + 1)
                    }
                }
            })}>Add</Button>
        </Spin>
    );
};


const formatTreeData = (data: any[]) => {
    return data.map(item => {
        const { id, name } = item;
        return {
            key: id.toString(),
            title: name.toString(),
            children: item.children ? formatTreeData(item.children) : undefined,
        };
    });
};

const highlightText = (node/*text: string*/, search: string) => {
    const text = node.title as string;
    const id = node.key as string;
    //if (!search) return nodeHtml(id, text);
    if (!search) return text;
    const parts = text.split(new RegExp(`(${search})`, 'gi'));
    const highlightedText = parts.map((part, index) =>
        part.toLowerCase() === search.toLowerCase() ? (
            <span key={index} style={{ backgroundColor: 'yellow' }}>{part}</span>
        ) : (
            part
        )
    );
    return highlightedText
    //return nodeHtml(id, highlightedText as string);
};

const nodeHtml = (id: string, title: string, reload: () => void = () => {}) => {
    return (<div>
        <Space>
            <span className="title">{title}</span>
            {/*<Link to={route('notes.categories.edit', {category_id: id})}>edit</Link>*/}
            <Button type="link" htmlType="button" onClick={() => showModal(<NoteCategoryFormPage />,  {
                title: "Add category",
                loading: true,
                data: {parentId: id},
                onClose: (data) => {
                    if (data?.reload) {
                        setReloadTrigger(prev => prev + 1)
                    }
                }
            })}>add</Button>
            <Button type="link" onClick={() => showModal(<NoteCategoryFormPage />,  {
                title: "Edit category",
                //closeButton: true,
                loading: true,
                data: {id: id},
                onClose: (data) => {
                    if (data?.reload) {
                        reload();
                    }
                }
            })}>edit</Button>
            <Confirmable onConfirm={() => {deleteCategory(id, reload)}}>
                <Button type="link">delete</Button>
            </Confirmable>
        </Space>
    </div>)
}

const deleteCategory = async (categoryId, reload) => {
    const response = await api.safeRequest(`notes.categories.delete`, {category_id: categoryId});
    if (response?.data?.success) {
        reload();
        message.success('Category was deleted successfully')
    } else {
        message.error('Deleting failed');
    }
}


