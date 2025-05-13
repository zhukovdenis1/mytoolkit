import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';
import Table from '@tiptap/extension-table';
import TableRow from '@tiptap/extension-table-row';
import TableHeader from '@tiptap/extension-table-header';
import {CustomTableCell} from './tableConfig.tsx';

export const getUserConfig = (mode: 'view' | 'edit' = 'edit') => [
    StarterKit,
    Link.configure({ openOnClick: (mode === 'view') }),
    Table.configure({ resizable: true }),
    TableRow,
    TableHeader,
    CustomTableCell
];
