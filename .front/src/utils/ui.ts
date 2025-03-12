//import type { TreeDataNode } from '@ui/types';
import type { DataNode } from 'rc-tree/lib/interface'; // Добавляем тип DataNode

export const convertTreeData = (
    data: any[],
    convertFields: Record<string, string> = { id: "value", name: "title" }
): DataNode[] => { // Указываем, что функция возвращает DataNode[]
    return data.map(item => {
        const convertedItem: Record<string, any> = {};

        // Меняем ключи на основе convertFields
        for (const [originalKey, newKey] of Object.entries(convertFields)) {
            if (originalKey in item) {
                convertedItem[newKey] = item[originalKey].toString();
            }
        }

        // Обязательно добавляем поле key (предположим, что оно должно быть item.id или аналогично)
        convertedItem.key = item.id ? String(item.id) : String(Math.random()); // или используйте item[convertFields.id], если id это поле в convertFields

        // Рекурсивно преобразуем дочерние элементы, если есть
        if (item.children) {
            convertedItem.children = convertTreeData(item.children, convertFields);
        }

        return convertedItem as DataNode; // Приводим объект к типу DataNode
    });
};
