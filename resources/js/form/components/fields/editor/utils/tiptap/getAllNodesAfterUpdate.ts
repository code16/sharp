import { Transaction } from '@tiptap/pm/state';
import type { Node as ProseMirrorNode } from '@tiptap/pm/model';
import { Transform } from '@tiptap/pm/transform';
import { findChildren } from "@tiptap/core";


export function getAllNodesAfterUpdate(name: string, transaction: Transaction, appendedTransactions: Transaction[]) {
    const nextTransaction = combineTransactionSteps(transaction.before, [transaction, ...appendedTransactions]);

    return findChildren(nextTransaction.doc, node => node.type.name === name).map(node => node.node);
}

/**
 * @see import('@tiptap/core/src/helpers/combineTransactionSteps')
 */
function combineTransactionSteps(oldDoc: ProseMirrorNode, transactions: Transaction[]): Transform {
    const transform = new Transform(oldDoc)

    transactions.forEach(transaction => {
        transaction.steps.forEach(step => {
            transform.step(step)
        })
    })

    return transform
}
