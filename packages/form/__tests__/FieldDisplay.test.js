import Vue from 'vue';
import FieldContainer from '../src/components/ui/FieldContainer.vue';
import FieldDisplay from '../src/components/FieldDisplay.vue';
import * as conditions from '../src/util/conditional-display';

import { shallowMount } from '@vue/test-utils';


jest.mock('../src/components/ui/FieldContainer.vue', () => ({
    name: 'FieldContainer',
    template: '<div class="FIELD_CONTAINER_MOCK"></div>',
}));

describe('field-display', () => {

    function createWrapper({ propsData, provide }) {
        return shallowMount(FieldDisplay, {
            context: {
                props: {
                    fieldKey: 'title',
                    configIdentifier: 'title',
                    errorIdentifier: 'title',
                    ...propsData,
                },
            },
            provide: {
                $form: new Vue({ data: { localized: false } }),
                ...provide,
            }
        });
    }

    let computeSelectCondition = ()=>{}, computeCondition = ()=>{};

    beforeEach(() => {
        computeSelectCondition = conditions.helpers.computeSelectCondition = jest.fn(conditions.helpers.computeSelectCondition);
        computeCondition = conditions.computeCondition = jest.fn(conditions.computeCondition);
    });

    test('can mount field display', () => {
        const wrapper = createWrapper({
            propsData: {
                contextData: {
                    title: null
                },
                contextFields: {
                    title: { type: 'text' }
                }
            },
        });

        expect(wrapper.html()).toMatchSnapshot();
    });

    describe('conditional diplay', ()=>{

        test('check',  () => {
            let wrapper = null;
            const contextFields = {
                title: {
                    type: 'text',
                    conditionalDisplay: {
                        operator: 'or',
                        fields: [{ key:'check', values: true }]
                    }
                },
                check: { type: 'check' }
            };
            const contextData = {
                title: null,
                check: true
            };

            wrapper = createWrapper({
                propsData: {
                    contextData,
                    contextFields,
                }
            });
            expect(wrapper.findAll(FieldContainer).length).toBe(1);
            expect(computeCondition)
                .toHaveBeenCalledWith(contextFields, contextData, contextFields.title.conditionalDisplay);

            wrapper = createWrapper({
                propsData: {
                    contextData: {
                        ...contextData,
                        check: false,
                    },
                    contextFields,
                },
            })
            expect(wrapper.findAll(FieldContainer).length).toBe(0);
        });

        test('multiple select (unique values)', async () => {
            let wrapper = null;
            const contextFields = {
                title: {
                    type: 'text',
                    conditionalDisplay: {
                        operator: 'or',
                        fields: [
                            { key:'select', values: 2 }
                        ]
                    }
                },
                select: {
                    type: 'select',
                    multiple: true
                }
            };

            wrapper = createWrapper({
                propsData: {
                    contextFields,
                    contextData: {
                        title: null,
                        select: [2, 3]
                    },
                },
            });
            expect(computeSelectCondition).toHaveBeenCalledWith({ condValues: 2, fieldValue: [2,3], isSingleSelect: false });
            expect(wrapper.findAll(FieldContainer).length).toBe(1);


            wrapper = createWrapper({
                propsData: {
                    contextFields,
                    contextData: {
                        title: null,
                        select: [3]
                    },
                }
            });
            expect(computeSelectCondition).toHaveBeenCalledWith({ condValues: 2, fieldValue: [3], isSingleSelect: false });
            expect(wrapper.findAll(FieldContainer).length).toBe(0);
        });

        test('multiple select (multiple values)', () => {
            let wrapper = null;
            const contextFields = {
                title: {
                    type: 'text',
                    conditionalDisplay: {
                        operator: 'or',
                        fields: [
                            { key:'select', values: [ 2, 6 ] }
                        ]
                    }
                },
                select: {
                    type: 'select',
                    multiple: true
                }
            }


            wrapper = createWrapper({
                propsData: {
                    contextFields,
                    contextData: {
                        title: null,
                        select: [2, 3]
                    }
                }
            });
            expect(computeSelectCondition).toHaveBeenLastCalledWith({ condValues: [2,6], fieldValue: [2,3], isSingleSelect: false });
            expect(wrapper.findAll(FieldContainer).length).toBe(1);

            wrapper = createWrapper({
                propsData: {
                    contextFields,
                    contextData: {
                        title: null,
                        select: [4, 5]
                    }
                }
            });
            expect(computeSelectCondition).toHaveBeenLastCalledWith({ condValues: [2,6], fieldValue: [4,5], isSingleSelect: false });
            expect(wrapper.findAll(FieldContainer).length).toBe(0);
        });

        test('single select (unique values)', () => {
            let wrapper = null;
            const contextFields = {
                title: {
                    type: 'text',
                    conditionalDisplay: {
                        operator: 'or',
                        fields: [
                            { key:'select', values: 4 }
                        ]
                    }
                },
                select: {
                    type: 'select'
                }
            }

            wrapper = createWrapper({
                propsData: {
                    contextFields,
                    contextData: {
                        title: null,
                        select: 4
                    }
                }
            })
            expect(computeSelectCondition).toHaveBeenLastCalledWith({ condValues: 4, fieldValue: 4, isSingleSelect: true });
            expect(wrapper.findAll(FieldContainer).length).toBe(1);


            wrapper = createWrapper({
                propsData: {
                    contextFields,
                    contextData: {
                        title: null,
                        select: 5
                    }
                }
            })
            expect(computeSelectCondition).toHaveBeenLastCalledWith({ condValues: 4, fieldValue: 5, isSingleSelect: true });
            expect(wrapper.findAll(FieldContainer).length).toBe(0);
        });

        test('single select (multiple values)', () => {
            let wrapper = null;
            const contextFields = {
                title: {
                    type: 'text',
                    conditionalDisplay: {
                        operator: 'or',
                        fields: [
                            { key:'select', values: [4, 8] }
                        ]
                    }
                },
                select: {
                    type: 'select'
                }
            }

            wrapper = createWrapper({
                propsData: {
                    contextFields,
                    contextData: {
                        title: null,
                        select: 4,
                    }
                }
            });
            expect(computeSelectCondition).toHaveBeenLastCalledWith({ condValues: [4,8], fieldValue: 4, isSingleSelect: true });
            expect(wrapper.findAll(FieldContainer).length).toBe(1);

            wrapper = createWrapper({
                propsData: {
                    contextFields,
                    contextData: {
                        title: null,
                        select: 3,
                    }
                }
            });
            expect(computeSelectCondition).toHaveBeenLastCalledWith({ condValues: [4,8], fieldValue: 3, isSingleSelect: true });
            expect(wrapper.findAll(FieldContainer).length).toBe(0);
        });

        test('multiple select (negative)', async () => {
            let wrapper = null;
            const contextFields = {
                title: {
                    type: 'text',
                    conditionalDisplay: {
                        operator: 'or',
                        fields: [
                            { key:'select', values: '!5' }
                        ]
                    }
                },
                select: {
                    type: 'select',
                    multiple: true
                }
            };

            wrapper = createWrapper({
                propsData: {
                    contextFields,
                    contextData: {
                        title: null,
                        select: [4, 6]
                    }
                }
            });
            expect(computeSelectCondition).toHaveBeenLastCalledWith({ condValues: '!5', fieldValue: [4,6], isSingleSelect: false });
            expect(wrapper.findAll(FieldContainer).length).toBe(1);


            wrapper = createWrapper({
                propsData: {
                    contextFields,
                    contextData: {
                        title: null,
                        select: [5, 7]
                    }
                }
            });
            expect(computeSelectCondition).toHaveBeenLastCalledWith({ condValues: '!5', fieldValue: [5,7], isSingleSelect: false });
            expect(wrapper.findAll(FieldContainer).length).toBe(0);
        });

        test('single select (negative)', () => {
            let wrapper = null;
            const contextFields = {
                title: {
                    type: 'text',
                    conditionalDisplay: {
                        operator: 'or',
                        fields: [
                            { key:'select', values: '!6' }
                        ]
                    }
                },
                select: {
                    type: 'select'
                }
            }

            wrapper = createWrapper({
                propsData: {
                    contextFields,
                    contextData: {
                        title: null,
                        select: 3
                    },
                }
            });
            expect(computeSelectCondition).toHaveBeenLastCalledWith({ condValues: '!6', fieldValue: 3, isSingleSelect: true });
            expect(wrapper.findAll(FieldContainer).length).toBe(1);

            wrapper = createWrapper({
                propsData: {
                    contextFields,
                    contextData: {
                        title: null,
                        select: 6,
                    },
                }
            });
            expect(computeSelectCondition).toHaveBeenLastCalledWith({ condValues: '!6', fieldValue: 6, isSingleSelect: true });
            expect(wrapper.findAll(FieldContainer).length).toBe(0);
        });

        test('or operator', async () => {
            let wrapper = null;
            const contextFields = {
                title: {
                    type: 'text',
                    conditionalDisplay: {
                        operator: 'or',
                        fields: [
                            { key:'check1', values: true },
                            { key:'check2', values: true }
                        ]
                    }
                },
                check1: { type: 'check' },
                check2: { type: 'check' }
            }

            wrapper = createWrapper({
                propsData: {
                    contextFields,
                    contextData: {
                        title: null,
                        check1: true,
                        check2: true,
                    },
                }
            });
            expect(wrapper.findAll(FieldContainer).length).toBe(1);

            wrapper = createWrapper({
                propsData: {
                    contextFields,
                    contextData: {
                        title: null,
                        check1: false,
                        check2: true,
                    },
                }
            });
            expect(wrapper.findAll(FieldContainer).length).toBe(1);

            wrapper = createWrapper({
                propsData: {
                    contextFields,
                    contextData: {
                        title: null,
                        check1: false,
                        check2: false,
                    },
                }
            });
            expect(wrapper.findAll(FieldContainer).length).toBe(0);
        });

        test('and operator', async () => {
            let wrapper = null;
            const contextFields = {
                title: {
                    type: 'text',
                    conditionalDisplay: {
                        operator: 'and',
                        fields: [
                            { key:'check1', values: true },
                            { key:'check2', values: true }
                        ]
                    }
                },
                check1: { type: 'check' },
                check2: { type: 'check' }
            };

            wrapper = createWrapper({
                propsData: {
                    contextFields,
                    contextData: {
                        title: null,
                        check1: true,
                        check2: true,
                    },
                }
            })
            expect(wrapper.findAll(FieldContainer).length).toBe(1);

            wrapper = createWrapper({
                propsData: {
                    contextFields,
                    contextData: {
                        title: null,
                        check1: true,
                        check2: false,
                    },
                }
            });
            expect(wrapper.findAll(FieldContainer).length).toBe(0);
        });
    });

    test('expose appropriate props', async () => {
        let wrapper = null;
        const propsData = {
            contextData: {
                title: 'myTitle',
            },
            contextFields: {
                title: {
                    type: 'text',
                    placeholder: 'Title',
                    label: 'Super title label',
                    helpMessage: 'Super help message'
                }
            },
        }

        wrapper = createWrapper({
            propsData,
        });

        expect(wrapper.find(FieldContainer).vm.$attrs).toMatchObject({
            fieldKey: 'title',
            fieldType: 'text',
            fieldProps: {
                type: 'text',
                placeholder: 'Title'
            },
            value: 'myTitle',
            label: 'Super title label',
            helpMessage: 'Super help message'
        });

        wrapper = createWrapper({
            propsData: {
                ...propsData,
                readOnly: true,
            },
        })

        expect(wrapper.find(FieldContainer).vm.$attrs).toMatchObject({
            fieldProps: {
                type: 'text',
                placeholder: 'Title',
                readOnly: true,
            },
        });
    });

    test('call update visibility',  () => {
        let wrapper = null;
        const updateVisibility = jest.fn();
        const contextFields = {
            title: {
                type: 'text',
                conditionalDisplay: {
                    operator: 'or',
                    fields: [{ key:'check', values: true }]
                }
            },
            check: { type: 'check' }
        };

        wrapper = createWrapper({
            propsData: {
                contextFields,
                contextData: {
                    title: 'myTitle',
                    check: true,
                },
                updateVisibility,
            }
        });
        expect(updateVisibility).toHaveBeenCalledTimes(1);
        expect(updateVisibility).toHaveBeenLastCalledWith('title',true);

        wrapper = createWrapper({
            propsData: {
                contextFields,
                contextData: {
                    title: 'myTitle',
                    check: false,
                },
                updateVisibility,
            }
        });
        expect(updateVisibility).toHaveBeenCalledTimes(2);
        expect(updateVisibility).toHaveBeenLastCalledWith('title',false);
    });

    test('handle localized value', async () => {

        function resolveValue({
            type,
            value,
            locale,
            localized = true,
            $form = { localized: true }
        }) {
            const wrapper = createWrapper({
                propsData: {
                    contextData: {
                        title: value,
                    },
                    contextFields: {
                        title: { type, localized },
                    },
                    locale,
                },
                provide: {
                    $form: new Vue({ data:$form }),
                }
            });
            return wrapper.find(FieldContainer).vm.$attrs.value;
        }

        expect(
            resolveValue({
                type: 'text',
                value: null,
            })
        ).toBe(null);

        expect(
            resolveValue({
                type: 'text',
                value: { fr:'texte FR', en:'texte EN' },
                locale: 'fr',
            })
        ).toBe('texte FR');

        expect(
            resolveValue({
                type: 'textarea',
                value: { fr:'texte FR', en:'texte EN' },
                locale: 'fr',
            })
        ).toBe('texte FR');

        expect(
            resolveValue({
                type: 'markdown',
                value: { fr:'texte FR', en:'texte EN' },
                locale: 'fr'
            })
        ).toEqual({ fr:'texte FR', en:'texte EN' });

        expect(
            resolveValue({
                type: 'wysiwyg',
                value: { fr:'texte FR', en:'texte EN' },
                locale: 'fr'
            })
        ).toEqual({ fr:'texte FR', en:'texte EN' });

        expect(
            resolveValue({
                type: 'text',
                value: { fr:'texte FR', en:'texte EN' },
                localized: false,
            })
        ).toEqual({ fr:'texte FR', en:'texte EN' });

        expect(
            resolveValue({
                type: 'text',
                value: { fr:'texte FR', en:'texte EN' },
                $form: { localized: false },
            })
        ).toEqual({ fr:'texte FR', en:'texte EN' });
    });

    xtest('handle localized identifier', () => {
        let wrapper = null;

        wrapper = createWrapper({
            contextData: {
                title: null,
            },
            contextFields: {
                title: { type: 'text', localized: true  },
            },
            locale: 'fr'
        });
        expect(wrapper.find(FieldContainer).vm.$attrs.errorIdentifier).toEqual('title.fr');

        wrapper = createWrapper({
            contextData: {
                title: null,
            },
            contextFields: {
                title: { type: 'text', localized: false  },
            },
            locale: 'fr'
        });
        expect(wrapper.find(FieldContainer).vm.$attrs.errorIdentifier).toEqual('title');
    });
});
