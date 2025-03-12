import '@/css/ui/tiptap.css';
import {
    BubbleMenu,
    EditorContent,
    useEditor,
} from '@tiptap/react';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';
import { Table } from '@tiptap/extension-table';
import { TableRow } from '@tiptap/extension-table-row';
import { TableHeader } from '@tiptap/extension-table-header';
import { TableCell } from '@tiptap/extension-table-cell';
import React, { useState } from 'react';
import LinkModal from './LinkModal';
import { buildLinkAttributes } from './LinkHelpers';
import {
    UnorderedListOutlined,
    TableOutlined,
    LinkOutlined,
    PlusSquareOutlined,
    PlusOutlined ,
    MinusSquareOutlined,
    MinusOutlined,
    SmallDashOutlined
} from '../icons'
import { Dropdown } from 'antd';
import { useEffect } from "react";


import type { VisualEditorProps, LinkData } from './types';
//🔗

const VisualEditor: React.FC<VisualEditorProps> = ({ value, onChange, disabled, mode }) => {
    disabled;
    const [isLinkModalOpen, setLinkModalOpen] = useState(false);
    const [linkData, setLinkData] = useState<LinkData>({
        href: '',
        target: '_self',
        class: '',
    });

    const CustomTableCell = TableCell.extend({
        addAttributes() {
            return {
                // extend the existing attributes …
                ...this.parent?.(),

                // and add a new one …
                backgroundColor: {
                    default: null,
                    parseHTML: element => element.getAttribute('data-background-color'),
                    renderHTML: attributes => {
                        return {
                            'data-background-color': attributes.backgroundColor,
                            style: `background-color: ${attributes.backgroundColor}`,
                        }
                    },
                },
            }
        },
    })

    const editor = useEditor({
        extensions: [
            StarterKit,
            Link.configure({ openOnClick: (mode === 'view') }),
            Table.configure({
                resizable: true,
            }), // Добавляем расширение Table
            TableRow, // Добавляем расширение TableRow
            TableHeader, // Добавляем расширение TableHeader
            //TableCell, // Добавляем расширение TableCell
            CustomTableCell
        ],
        content: value,
        onUpdate: ({ editor }) => {return onChange(editor.getHTML())},
    });

    // После инициализации редактора
    useEffect(() => {
        if (editor && editor.getHTML() !== value) {
            editor.commands.setContent(value, false); // false — без сохранения в историю (чтобы не засорять undo)
        }
    }, [value, editor]);

    const openLinkModal = () => {
        if (!editor) return;

        const { href = '', target = '_self', class: className = '' } = editor.getAttributes('link');

        setLinkData({ href, target, class: className });
        setLinkModalOpen(true);
    };

    const applyLink = (data: LinkData) => {
        const attributes = buildLinkAttributes(data);
        if (data.href) {
            editor?.chain().focus().setLink(attributes).run();
        } else {
            editor?.chain().focus().unsetLink().run();
        }
        setLinkModalOpen(false);
    };

    // Меню для операций с таблицами
    const tableMenu = {
        items: [
            {
                key: "insert-table",
                label: "Table",
                icon: <PlusSquareOutlined />,
                onClick: () => editor?.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run(),
            },
            {
                key: "add-column",
                label: "Column",
                icon: <PlusOutlined />,
                disabled: !editor?.can().addColumnAfter(),
                onClick: () => editor?.chain().focus().addColumnAfter().run(),
            },
            {
                key: "add-row",
                label: "Row",
                icon: <PlusOutlined />,
                disabled: !editor?.can().addRowAfter(),
                onClick: () => editor?.chain().focus().addRowAfter().run(),
            },
            {
                key: "delete-column",
                label: "Delete Column",
                icon: <MinusOutlined />,
                disabled: !editor?.can().deleteColumn(),
                onClick: () => editor?.chain().focus().deleteColumn().run(),
            },
            {
                key: "delete-row",
                label: "Delete Row",
                icon: <MinusOutlined />,
                disabled: !editor?.can().deleteRow(),
                onClick: () => editor?.chain().focus().deleteRow().run(),
            },
            {
                key: "extra",
                label: "More Actions",
                icon: <SmallDashOutlined />,
                children: [
                    {
                        key: "add-row-before",
                        label: "Row Before",
                        icon: <PlusOutlined />,
                        disabled: !editor?.can().addRowBefore(),
                        onClick: () => editor?.chain().focus().addRowBefore().run(),
                    },
                    {
                        key: "add-column-before",
                        label: "Column Before",
                        icon: <PlusOutlined />,
                        disabled: !editor?.can().addColumnBefore(),
                        onClick: () => editor?.chain().focus().addColumnBefore().run(),
                    },
                    {
                        key: "toggle-header-row",
                        label: "Toggle Header Row",
                        disabled: !editor?.can().toggleHeaderRow(),
                        onClick: () => editor?.chain().focus().toggleHeaderRow().run(),
                    },
                    {
                        key: "toggle-header-cell",
                        label: "Toggle Header Cell",
                        disabled: !editor?.can().toggleHeaderCell(),
                        onClick: () => editor?.chain().focus().toggleHeaderCell().run(),
                    },
                    {
                        key: "merge-or-split",
                        label: "Merge or Split",
                        disabled: !editor?.can().mergeOrSplit(),
                        onClick: () => editor?.chain().focus().mergeOrSplit().run(),
                    },
                    {
                        key: "set-cell-attribute",
                        label: "Set Cell Background",
                        disabled: !editor?.can().setCellAttribute('backgroundColor', '#FAF594'),
                        onClick: () => editor?.chain().focus().setCellAttribute('backgroundColor', '#FAF594').run(),
                    },
                ],
            },
            {
                key: "delete-table",
                label: "Delete Table",
                icon: <MinusSquareOutlined />,
                disabled: !editor?.can().deleteTable(),
                onClick: () => editor?.chain().focus().deleteTable().run(),
            },
        ],
    };


    return (
        <div className="visual-editor-container">
            {editor && (
                <BubbleMenu className="bubble-menu" tippyOptions={{duration: 100, maxWidth: 'auto'}} editor={editor}>
                    <button
                        type="button"
                        onClick={() => editor.chain().focus().toggleBold().run()}
                        className={editor.isActive('bold') ? 'is-active' : ''}
                    >
                        <b>B</b>
                    </button>
                    <button
                        type="button"
                        onClick={() => editor.chain().focus().toggleItalic().run()}
                        className={editor.isActive('italic') ? 'is-active' : ''}
                    >
                        <i>I</i>
                    </button>
                    <button
                        type="button"
                        onClick={() => editor.chain().focus().toggleStrike().run()}
                        className={editor.isActive('strike') ? 'is-active' : ''}
                    >
                        <s>S</s>
                    </button>
                    <button
                        type="button"
                        onClick={() => editor.chain().focus().toggleBulletList().run()}
                        className={editor.isActive('bulletList') ? 'is-active' : ''}
                    >
                        <UnorderedListOutlined />
                    </button>
                    <button
                        type="button"
                        onClick={() => editor.chain().focus().toggleCode().run()}
                        className={editor.isActive('code') ? 'is-active' : ''}
                    >
                        &lt;&gt;
                    </button>
                    <button
                        type="button"
                        onClick={() => editor.chain().focus().toggleHeading({level: 2}).run()}
                        className={editor.isActive('heading', {level: 2}) ? 'is-active' : ''}
                    >
                        H2
                    </button>
                    <button
                        type="button"
                        onClick={() => editor.chain().focus().toggleHeading({level: 3}).run()}
                        className={editor.isActive('heading', {level: 3}) ? 'is-active' : ''}
                    >
                        H3
                    </button>
                    <button
                        title="Link"
                        type="button"
                        onClick={openLinkModal}
                        className={editor.isActive('link') ? 'is-active' : ''}
                    >
                        <LinkOutlined />
                    </button>
                    <Dropdown menu={tableMenu} trigger={['click']}>
                        <button
                            title="Table operations"
                            type="button"
                        >
                            <TableOutlined />
                        </button>
                    </Dropdown>
                </BubbleMenu>
            )}

            <div className="visual-editor-content">
                <EditorContent editor={editor}/>
            </div>

            {isLinkModalOpen && (
                <LinkModal
                    initialData={linkData}
                    onSave={applyLink}
                    onCancel={() => setLinkModalOpen(false)}
                    onUnlink={() => {
                        editor?.chain().focus().unsetLink().run();
                        setLinkModalOpen(false);
                    }}
                    isEditingLink={editor?.isActive('link') ?? false}
                />
            )}
        </div>
    );
};

export default VisualEditor;
