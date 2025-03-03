import React, { useEffect, useState } from "react";
import { api, route } from "api";
import {Link} from "react-router-dom";
import {NodeEditor} from './NodeEditor'


interface NoteNode {
    id: number;
    name: string;
    children?: NoteNode[];
}

export const SubNoteList: React.FC<{
    parentId?: string|number
}> = ({ parentId }) => {
    const [list, setList] = useState<NoteNode[]>([]);

    useEffect(() => {
        const fetchData = async () => {
            const response = await api.safeRequest(`notes.tree`, { parent_id: parentId });
            if (response && typeof response !== 'boolean' && response.data) {
                setList(response.data.data);
            }
        }
        fetchData();
    }, [parentId]);

    const renderTree = (nodes: NoteNode[]) => {
        return (
            <ul>
                {nodes.map(node => (
                    <li key={node.id}>
                        <Link to={route('notes.view', {note_id: node.id})}>{node.name}</Link>
                        <NodeEditor node={node} />
                        {node.children && node.children.length > 0 && (
                            <div style={{ marginLeft: 20 }}>
                                {renderTree(node.children)}
                            </div>
                        )}
                    </li>
                ))}
            </ul>
        );
    };

    return (
        <>
            {list.length > 0 ? <div className="note-menu">{renderTree(list)}</div> : <></>}
        </>
    );
};




