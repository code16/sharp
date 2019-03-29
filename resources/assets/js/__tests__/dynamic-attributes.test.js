import * as dynamicAttributes from '../components/form/dynamic-attributes';
import * as dynamicAttributesUtil from '../components/form/dynamic-attributes/util';

import {
    getDynamicAttributeOptions,
    getValueFromMap,
    getValueFromTemplate,
    getSourcesFromTemplate,
    resolveValue
} from '../components/form/dynamic-attributes';

import {
    getEmptySources,
    warnEmptyValue,
} from '../components/form/dynamic-attributes/util';

describe('Dynamic attributes', () => {
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

    xtest('getValueFromTemplate', () => {
        expect(getValueFromTemplate({
            template: '{{name}}/{{year}}',
            contextData: {
                name: 'George',
                year: 2019,
            }
        })).toBe('George/2019');
    });

    test('getSourcesFromTemplate', () => {
        expect(getSourcesFromTemplate('{{name}}/{{year}}/{{empty}}'))
            .toEqual(['name', 'year', 'empty']);
    });

    describe('resolveValue', () => {
        beforeEach(() => {

            jest.spyOn(dynamicAttributesUtil, 'warnEmptyValue').mockImplementation();
            jest.spyOn(dynamicAttributesUtil, 'getEmptySources').mockImplementation(()=>[]);
        });
        afterEach(() => {
            jest.restoreAllMocks();
        });
        test('map', () => {
            jest.spyOn(dynamicAttributes, 'getValueFromMap').mockImplementation();
            jest.spyOn(dynamicAttributes, 'getDynamicAttributeOptions').mockImplementation(()=>({
                name: 'options',
                type: 'map',
                path: ['brand_name'],
            }));
            dynamicAttributes.resolveValue({
                sourceName: 'field',
                attributeName: 'options',
                attributeValue: { 1:[] },
                contextData: {
                    brand_name: 'Renault',
                }
            });
            expect(dynamicAttributes.getValueFromMap).toHaveBeenCalledWith({
                map: { 1:[] },
                path: ['brand_name'],
                contextData: {
                    brand_name: 'Renault',
                }
            });
        });
    });
});