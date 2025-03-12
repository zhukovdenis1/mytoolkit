import React, { useState } from 'react';
import { Input, Tree } from 'antd';
import { DownOutlined } from '@ant-design/icons';
import type { TreeDataNode } from 'antd';

export const DemoTreeCategoriesPage: React.FC = () => {
    const [search, setSearch] = useState('');
    const [treeData, setTreeData] = useState(originalTreeData);
    const [expandedKeys, setExpandedKeys] = useState<string[]>([]);

    const onSearch = (e: React.ChangeEvent<HTMLInputElement>) => {
        const value = e.target.value;
        setSearch(value);
        const [filteredData, keys] = filterTree(originalTreeData, value);
        setTreeData(filteredData);
        setExpandedKeys(keys);
    };

    return (
        <div>
            <Input placeholder="Search" value={search} onChange={onSearch} style={{ marginBottom: 8 }} />
            <Tree
                showLine
                switcherIcon={<DownOutlined />}
                expandedKeys={expandedKeys}
                onExpand={setExpandedKeys}
                treeData={treeData}
            />
        </div>
    );
};

const originalTreeData: TreeDataNode[] = [
    {
        title: 'parent 1',
        key: '0-0',
        children: [
            {
                title: 'parent 1-0',
                key: '0-0-0',
                children: [
                    { title: 'leaf', key: '0-0-0-0' },
                    { title: 'leaf', key: '0-0-0-1' },
                    { title: 'leaf', key: '0-0-0-2' },
                ],
            },
            {
                title: 'parent 1-1',
                key: '0-0-1',
                children: [{ title: 'leaf', key: '0-0-1-0' }],
            },
            {
                title: 'parent 1-2',
                key: '0-0-2',
                children: [
                    { title: 'leaf', key: '0-0-2-0' },
                    { title: 'leaf', key: '0-0-2-1' },
                ],
            },
        ],
    },
];

const highlightText = (text: string, search: string) => {
    if (!search) return text;
    const parts = text.split(new RegExp(`(${search})`, 'gi'));
    return parts.map((part, index) =>
        part.toLowerCase() === search.toLowerCase() ? (
            <span key={index} style={{ backgroundColor: 'yellow' }}>{part}</span>
        ) : (
            part
        )
    );
};

const filterTree = (data: TreeDataNode[], search: string): [TreeDataNode[], string[]] => {
    let expandedKeys: string[] = [];

    const filterNode = (node: TreeDataNode): TreeDataNode | null => {
        if (node.children) {
            const filteredChildren = node.children.map(filterNode).filter(Boolean) as TreeDataNode[];
            if (filteredChildren.length > 0 || node.title.toLowerCase().includes(search.toLowerCase())) {
                expandedKeys.push(node.key.toString());
                return { ...node, children: filteredChildren, title: highlightText(node.title as string, search) };
            }
        }
        return node.title.toLowerCase().includes(search.toLowerCase())
            ? { ...node, title: highlightText(node.title as string, search) }
            : null;
    };

    return [data.map(filterNode).filter(Boolean) as TreeDataNode[], expandedKeys];
};
