import * as dynamicAttributesUtil from '../components/form/dynamic-attributes/util';
import * as dynamicAttributesResolve from '../components/form/dynamic-attributes/resolve';

import {
    transformAttributes,
    hasDependency,
} from "../components/form/dynamic-attributes";

import {
    resolveValue,
    getContextSources,
} from '../components/form/dynamic-attributes/resolve';

import {
    getEmptyValueSources,
    getDynamicAttributeOptions,
    getValueFromMap,
    getValueFromTemplate,
    getSourcesFromTemplate,
} from '../components/form/dynamic-attributes/util';

describe('Dynamic attributes', () => {
    beforeEach(() => {
        jest.resetAllMocks();
    });

    test('getDynamicAttributesOptions', () => {
        expect(getDynamicAttributeOptions()).toBeUndefined();
        expect(getDynamicAttributeOptions([{ name:'attribute' }], 'other')).toBeUndefined();
        expect(getDynamicAttributeOptions([{ name:'other' }, { name:'attribute' }], 'attribute'))
            .toEqual({ name:'attribute' });
    });

    test('getValueForMap', () => {
        expect(
            getValueFromMap({
                map: {
                    'true': {
                        'Renault': {
                            '2017': 'value'
                        }
                    },
                },
                path: ['check', 'brand_name', 'year'],
                contextData: {
                    check: true,
                    brand_name: 'Renault',
                    year: 2017,
                },
            })
        ).toEqual('value');
    });

    test('getValueFromTemplate', () => {
        expect(getValueFromTemplate({
            template: '{{name}}/{{year}}',
            contextData: {
                name: 'George',
                year: 2019,
            },
            sources: ['name', 'year'],
        })).toBe('George/2019');
    });
    test('getValueFromTemplate should not throw', () => {
        expect(getValueFromTemplate({
            template: '{{name}}/{{year}}',
            contextData: {
                name: 'George',
            },
            sources: ['name', 'year'],
        })).toBe('George/');
    });

    test('getSourcesFromTemplate', () => {
        expect(getSourcesFromTemplate('{{name}}/{{year}}/{{empty}}'))
            .toEqual(['name', 'year', 'empty']);
    });

    test('getEmptyValueSources', () => {
        expect(getEmptyValueSources({
            contextSources: ['field1', 'field2', 'field3', 'field4', 'field5'],
            contextData: {
                field1: '',
                field2: null,
                field3: undefined,
                field4: 0,
                field5: 'aaa'
            },
        })).toEqual(['field1', 'field2', 'field3']);
    });

    test('getContextSources', () => {
        expect(getContextSources({
            dynamicOptions: { type:'map', path:'path' }
        })).toBe('path');

        jest.spyOn(dynamicAttributesUtil, 'getSourcesFromTemplate')
            .mockImplementation(()=>'templateResolvedSources');
        expect(getContextSources({
            dynamicOptions: { type:'template' },
            attributeValue: '/name'
        })).toBe('templateResolvedSources');
        expect(getSourcesFromTemplate).toHaveBeenCalledWith('/name');

        expect(getContextSources({})).toEqual([]);
    });

    describe('resolveValue', () => {
        beforeEach(() => {
            jest.spyOn(dynamicAttributesUtil, 'getEmptyValueSources').mockImplementation(()=>[]);
        });

        test('resolve not dynamic', () => {
            jest.spyOn(dynamicAttributesUtil, 'getDynamicAttributeOptions').mockImplementation();
            expect(
                resolveValue('attr', 'attrValue', {
                    dynamicAttributes: [{ name:'attr', }],
                })
            ).toEqual({ value:'attrValue' });
            expect(getDynamicAttributeOptions).toHaveBeenCalledWith([{ name:'attr', }], 'attr');
        });

        test('resolve map', () => {
            jest.spyOn(dynamicAttributesUtil, 'getValueFromMap').mockImplementation(()=>'resolvedValue');
            jest.spyOn(dynamicAttributesUtil, 'getDynamicAttributeOptions').mockImplementation(()=>({
                name: 'options',
                type: 'map',
                path: ['brand_name'],
            }));
            expect(
                resolveValue('options', { 1:[] }, {
                    contextData: {
                        brand_name: 'Renault',
                    },
                })
            ).toEqual({ value:'resolvedValue' });
            expect(getValueFromMap).toHaveBeenCalledWith({
                map: { 1:[] },
                path: ['brand_name'],
                contextData: {
                    brand_name: 'Renault',
                }
            });
        });

        test('resolve template', () => {
            jest.spyOn(dynamicAttributesUtil, 'getValueFromTemplate').mockImplementation(()=>'resolvedValue');
            jest.spyOn(dynamicAttributesUtil, 'getSourcesFromTemplate').mockImplementation(()=>['url']);
            jest.spyOn(dynamicAttributesUtil, 'getDynamicAttributeOptions').mockImplementation(()=>({
                name: 'url',
                type: 'template',
            }));
            expect(
                resolveValue('url', '/api/{{brand_name}}', {
                    contextData: {
                        year: 2016,
                    },
                })
            ).toEqual({ value: 'resolvedValue' });
            expect(getSourcesFromTemplate).toHaveBeenCalledWith('/api/{{brand_name}}');
            expect(getValueFromTemplate).toHaveBeenCalledWith({
                template: '/api/{{brand_name}}',
                sources: ['url'],
                contextData: {
                    year: 2016,
                },
            });
        });

        test('resolve default', () => {
            jest.spyOn(dynamicAttributesUtil, 'getEmptyValueSources').mockImplementation(()=>(['field']));
            jest.spyOn(dynamicAttributesUtil, 'getDynamicAttributeOptions').mockImplementation(()=>({
                default: 'defaultValue',
            }));
            expect(resolveValue('attr', 'value', {})).toEqual({ isDefault: true, value:'defaultValue', });
        });
    });

    test('transformAttributes', () => {
        jest.spyOn(dynamicAttributesResolve, 'resolveValue').mockImplementation(() => ({ value:'resolvedValue' }));
        expect(
            transformAttributes({
                localValues: [],
                placeholder: 'Enter text',
            }, [
                { name:'localValues' },
            ], {
                autocomplete: 4,
            })
        ).toEqual({
            attributes: {
                localValues: 'resolvedValue',
                placeholder: 'resolvedValue',
            },
            resolvedDefaultAttributes: [],
        });

        expect(resolveValue).toHaveBeenCalledWith('localValues', [], {
            dynamicAttributes: [{ name:'localValues' }],
            contextData: {
                autocomplete: 4,
            },
        });
    });

    test('transformAttributes with defaults', () => {
        jest.spyOn(dynamicAttributesResolve, 'resolveValue').mockImplementation(()=>({
            value:'defaultValue', isDefault: true
        }));
        expect(
            transformAttributes({
                localValues: [],
                placeholder: 'Enter text',
            }, null, null)
        ).toEqual({
            attributes: {
                localValues: 'defaultValue',
                placeholder: 'defaultValue',
            },
            resolvedDefaultAttributes: ['localValues', 'placeholder'],
        });
    });

    test('hasDependency', () => {
        jest.spyOn(dynamicAttributesResolve, 'getContextSources')
            .mockImplementation(() => ['autocomplete']);

        expect(
            hasDependency(
                'autocomplete', [
                    { name: 'options' }
                ],
                { options: [] },
            )
        ).toBe(true);
        expect(getContextSources).toHaveBeenCalledWith({
            dynamicOptions: { name: 'options' },
            attributeValue: [],
        });

        expect(
            hasDependency(
                'select', [
                    { name: 'dynamicAttribute' }
                ],
                { dynamicAttribute: [] },
            )
        ).toBe(false);
        expect(hasDependency()).toBe(false);
    });
});