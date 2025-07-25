import React, { useEffect, useState } from 'react';
import { Select, Spin } from 'antd';
//import type { SelectProps } from 'antd';
//import axios from 'axios';
import { api } from "api";

interface OptionType {
    value: string;
    label: string;
}

interface SearchInputProps {
    route: string;
    placeholder?: string;
    value?: string; // id категории
    onChange?: (value?: string) => void; // callback для работы с Antd Form
    style?: React.CSSProperties;
}

export const SearchInput: React.FC<SearchInputProps> = ({ route, placeholder, value, onChange, style }) => {
    const [options, setOptions] = useState<OptionType[]>([]);
    const [loading, setLoading] = useState(false);
    const [selectedValue, setSelectedValue] = useState<string | undefined>(value);

    useEffect(() => {
        if (value) {
            fetchNameById(value);
        }
    }, [value]);

    const fetchNameById = async (id: string) => {
        setLoading(true);

        const response = await api.safeRequest(route, {id: id});

        if (response.success) {
            const item = response.data.data[0]; // Берём первый элемент массива
            setOptions([{ value: item.id.toString(), label: item.name }]);
            setSelectedValue(item.id.toString());
        }

        setLoading(false);

    };

    const handleSearch = async (searchText: string) => {
        if (!searchText) {
            setOptions([]);
            return;
        }

        setLoading(true);

        //const response = await axios.get(route, { params: { search: searchText } });

        const response = await api.safeRequest(route, { search: searchText });

        if (response.success) {
            const fetchedOptions = response.data.data.map((item: any) => ({
                value: item.id.toString(),
                label: item.name,
            }));
            setOptions(fetchedOptions);
        }

        setLoading(false);

    };

    const handleChange = (newValue: string) => {
        setSelectedValue(newValue);
        if (onChange) {
            onChange(newValue); // важный момент для работы с Form.Item
        }
    };

    return (
        <Select
            showSearch
            value={selectedValue}
            placeholder={placeholder}
            style={style}
            filterOption={false}
            onSearch={handleSearch}
            onChange={handleChange}
            notFoundContent={loading ? <Spin size="small" /> : null}
            options={options}
            allowClear
        />
    );
};

