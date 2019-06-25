import {
    parseRange,
    serializeRange,
} from "../../util/querystring";

describe('querystring utils', () => {
    test('parseRange', () => {
        expect(parseRange(null)).toEqual({
            start: null,
            end: null,
        });
        expect(parseRange('..2018-06-05')).toEqual({
            start: null,
            end: '2018-06-05',
        });
        expect(parseRange('2019-06-01..')).toEqual({
            start: '2019-06-01',
            end: null,
        });
        expect(parseRange('2019-06-01..2018-06-05')).toEqual({
            start: '2019-06-01',
            end: '2018-06-05',
        });
    });

    test('serializeRange', () => {
        expect(serializeRange(null)).toEqual(null);

        expect(serializeRange({
            start: null,
            end: null,
        })).toEqual(null);

        expect(serializeRange({
            start: new Date(Date.UTC(2019, 5, 1)),
            end: null,
        })).toEqual('2019-06-01T00:00:00.000Z..');

        expect(serializeRange({
            start: null,
            end: new Date(Date.UTC(2019, 5, 5)),
        })).toEqual('..2019-06-05T00:00:00.000Z');

        expect(serializeRange({
            start: new Date(Date.UTC(2019, 5, 1)),
            end: new Date(Date.UTC(2019, 5, 5)),
        })).toEqual('2019-06-01T00:00:00.000Z..2019-06-05T00:00:00.000Z');

        expect(serializeRange({
            start: 0,
            end: 2,
        })).toEqual('0..2');
    });
});