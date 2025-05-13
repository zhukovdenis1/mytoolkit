import {TableCell} from "@tiptap/extension-table-cell";
import { Editor } from '@tiptap/core';
import {
    MinusOutlined,
    MinusSquareOutlined,
    PlusOutlined,
    PlusSquareOutlined,
    SmallDashOutlined
} from "@ui/editor/icons.tsx";

export const CustomTableCell = TableCell.extend({
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
});

export const getTableMenu  = (editor: Editor | null) => ({
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
    ]
});
