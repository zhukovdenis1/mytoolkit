import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';
import Table from '@tiptap/extension-table';
import TableRow from '@tiptap/extension-table-row';
import TableHeader from '@tiptap/extension-table-header';
import {CustomTableCell} from './tableConfig.tsx';
import { Extension } from '@tiptap/core';

// Расширение для разрешения всех атрибутов
const AllowAllAttributes = Extension.create({
    name: 'allowAllAttributes',
    addGlobalAttributes() {
        return [{
            types: ['*'],
            attributes: { '*': { default: null } }
        }];
    },
});

const configureUnsanitized = (ext: any) => ext.extend({
    addAttributes() {
        return {
            ...this.parent?.() || {},
            '*': { default: null }
        };
    }
});

export const getAdminConfig = (mode: 'view' | 'edit' = 'edit') => [
    AllowAllAttributes,
    StarterKit.configure({
        paragraph: {
            HTMLAttributes: { '*': null }
        },
        heading: {
            HTMLAttributes: { '*': null }
        },
        // Другие компоненты StarterKit...
    }),
    configureUnsanitized(Link).configure({ openOnClick: (mode === 'view') }),
    configureUnsanitized(Table).configure({ resizable: true }),
    configureUnsanitized(TableRow),
    configureUnsanitized(TableHeader),
    configureUnsanitized(CustomTableCell)
];
/*
export const getAdminConfig = (mode: 'view' | 'edit' = 'edit') => [
    AllowAllAttributes,
    StarterKit.configure({
        paragraph: {
            HTMLAttributes: { '*': null }
        },
        heading: {
            HTMLAttributes: { '*': null }
        },
        // Другие компоненты StarterKit...
    }),
    Link.extend({
        addAttributes() {
            return {
                ...this.parent?.() || {},
                '*': { default: null }
            };
        }
    }).configure({ openOnClick: mode === 'view' }),
    Table.extend({
        addAttributes() {
            return {
                ...this.parent?.() || {},
                '*': { default: null }
            };
        }
    }).configure({ resizable: true }),
    TableRow.extend({
        addAttributes() {
            return {
                ...this.parent?.() || {},
                '*': { default: null }
            };
        }
    }),
    TableHeader.extend({
        addAttributes() {
            return {
                ...this.parent?.() || {},
                '*': { default: null }
            };
        }
    }),
    CustomTableCell.extend({
        addAttributes() {
            return {
                ...this.parent?.() || {},
                '*': { default: null }
            };
        }
    })
];*/
