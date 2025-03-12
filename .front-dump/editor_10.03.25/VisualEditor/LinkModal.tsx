import React, { useState } from 'react';
import type { LinkData } from './types';

type Props = {
    initialData: LinkData;
    onSave: (data: LinkData) => void;
    onCancel: () => void;
    onUnlink: () => void;
    isEditingLink: boolean;
};

const LinkModal: React.FC<Props> = ({ initialData, onSave, onCancel, onUnlink, isEditingLink }) => {
    const [linkData, setLinkData] = useState<LinkData>(initialData);

    return (
        <div className="link-modal">
            <div className="modal-content">
                <h3>Настройки ссылки</h3>
                <label>
                    URL:
                    <input
                        type="text"
                        value={linkData.href}
                        onChange={(e) => setLinkData({ ...linkData, href: e.target.value })}
                    />
                </label>
                <label>
                    Target:
                    <select
                        value={linkData.target}
                        onChange={(e) => setLinkData({ ...linkData, target: e.target.value })}
                    >
                        <option value="_self">В текущем окне (_self)</option>
                        <option value="_blank">В новой вкладке (_blank)</option>
                        {/*<option value="_parent">В родительском окне (_parent)</option>*/}
                        {/*<option value="_top">Во всём окне (_top)</option>*/}
                    </select>
                </label>
                {/*<label>*/}
                {/*    CSS Class:*/}
                {/*    <input*/}
                {/*        type="text"*/}
                {/*        value={linkData.class}*/}
                {/*        onChange={(e) => setLinkData({ ...linkData, class: e.target.value })}*/}
                {/*    />*/}
                {/*</label>*/}

                <div className="modal-actions">
                    <button onClick={() => onSave(linkData)}>Сохранить</button>
                    {isEditingLink && (
                        <button onClick={onUnlink} style={{ marginLeft: 'auto', color: 'red' }}>
                            Удалить ссылку
                        </button>
                    )}
                    <button onClick={onCancel}>Отмена</button>
                </div>
            </div>
        </div>
    );
};

export default LinkModal;
