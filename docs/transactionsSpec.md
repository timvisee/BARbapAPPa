# Transactions specifications
Version: 0.1-draft (2017-08-26)

This document describes the basics of the transactions model BARbapAPPa uses.
It is technical and intended for developers.

## Transaction
A transaction defines, as the term suggests, a transaction.
Transactions are used to define and describe each type of money transfer,
whether that is a user buying a product, or a deposit.

Transaction examples:  
- A user buys products.
- A user deposits money.
- A user transfers money to another user.
- A refund for a failed transaction.

## Wallet
A wallet is a virtual purse that is owned by a user.
Wallets are used to quickly make payments to buy products,
so the user doesn't have to initiate a bank transfer for each transaction.

A wallet is created for a specific bar, and the wallet may only be used for payments on that specific bar in the future.
At the time of writing this document,
it is unsure whether multiple bars would allow the use of the same wallet if they share the same banking account.
This is in the current specification however, not possible.

Users should have the ability to deposit money to their virtual wallet with payment services or other methods.
The available options here are configured by the related bar the wallet is for.

Multiple named wallets may be created by a user for a single bar to allow better financial organization,
by using different wallets for different kind of transactions.

## Mutation
Transactions contain _Mutations_ to describe the money flow and behaviour for a transaction.

Transactions have a virtual transaction sum value.
Money is added to xor subtracted from this value by mutations.
Transactions therefore define the money flow.

Mutation examples:  
- Money is subtracted by a user buying products (subtracts from the transaction sum)
- Money is added xor subtracted from a users wallet (adds to xor subtracts from the transaction sum)
- Money is added by a deposit through a payment service (adds to the transaction sum)

A valid transaction should also have a resulting sum value of zero when all mutations are considered,
because money that flows somewhere, has to get from somewhere.
Therefore, transactions that have a sum value other than zero are inherently invalid.

Mutations only flow money in one direction.
To further clarify; mutations only add money to xor subtract money from the transaction sum.
If both directions are desired, two separate mutations are used.
Because of this, a transaction must have 2 or more mutations to be valid.

It is entirely possible to have two mutations for adding money to the transaction,
and a single mutation subtracting money from the transaction.
This would occur if a user buys a product for €10,
where two wallets were used for the payment both adding €5 euros.

To visualize an example case, this shows the money flow for a user buying a product for €10 with their virtual wallet.
```
              +-----------------------+       +-------------+
* Mutation 1: | User wallet           | ====> | Transaction |
              | - Payment for product |   |   | (Money sum) |
              +-----------------------+   |   +-------------+
                                          |          |
                                          |   +-------------+       +-----------------+
* Mutation 2:                             |   | Transaction | ====> | Bought products |
                                          |   | (Money sum) |   |   | - Costs money   |
                                          |   +-------------+   |   +-----------------+
                                          |                     |
                                          |                     |
                                        €10,-                 €10,-
                                    Wallet mutation      Product mutation
```

### Mutation constraints
For all mutations, some constraints apply regarding possibilities with transferring money between multiple accounts.

It is not possible to create a transaction to transfer money from one wallet to another,
where the two wallets are used for different bars (and thus, different banking accounts).

If mutations in a transaction add €10 to the transaction money sum,
that €10 euro must also be subtracted by other mutations related to the same bar (and banking account).

Transactions with mutations to multiple bars are possible if their bar specific money sums are all equal zero.

### Mutation types
There are various types of mutations that are attached to a transaction.
One mutation might modify the balance in a wallet,
while another mutation might define a user has bought products.

Types:  
- [Wallet mutation](#wallet-mutation)
- [Payment mutation](#payment-mutation)
- [Product mutation](#product-mutation)
- [Magic mutation](#magic-mutation)
- _Additional mutation types may be added in the future to support more transaction types._

Each type is further documented in it's own paragraph below.

#### Wallet mutation
A wallet mutation adds money to xor subtracts money from a users wallet.

The wallet that is _mutated_ is attached to this mutation instance.

If money is subtracted from a users wallet, the mutation `money` field will be positive because money is added to the transaction sum.
Therefore if money is added to a users wallet, the mutation `money` field will be negative.

#### Payment mutation
A payment mutation defines a money transfer using a supported payment service.

The payment (model) instance for this payment service transaction is attached to this mutation.
This attached model further defines the service that was actually used, the state of the payment,
and possibly some other relevant parameters.

This mutation would be used for a transaction of a user depositing money to a wallet.
If a user would deposit this money though PayPal or a similar service, 
this mutation defines the money flow for PayPal.

The success state of this mutation must be updated accordingly when the state of the attached payment changes.

If €10 would be deposited to a wallet, the payment mutation would have a `money` field value above zero,
as the mutation adds money to the transaction money sum.
Therefore if money was deposited to a user's payment service account (possibly a cashback to a bank account, because a payment failed)
the `money` field value would be below zero.

#### Product mutation
A product mutation defines money being consumed because a user buys any number of products.

Of course, the bought products are attached to this mutation state along with their individual price at time of buying and their quantities.

If a user would buy products for €10, the `money` field value would be €-10 because money is subtracted from the transaction sum.

Product mutations with a `money` field value of zero or above are currently not supported,
as these would define a mutation that a user receives money for supplying a product.
Support for this might be added in the future if there's a proper use case that requires this state.

#### Magic mutation
A magic mutation is a special kind of mutation, and it allows many special cases to be handled.
It is used for mutations that don't fit in the other mutation types available.

This kind of mutation doesn't explicitly specify what the mutation is for, with for example a attached payment or wallet.
Except for the fact that a `description` field is available on this mutation.
This should be filled in by the initiating user when possible to further describe what this magical mutation is about.
The `description` field is purely for accounting purposes and isn't interpreted by BARbapAPPa itself.

Additionally, a magic mutation isn't tied to a specific bar or banking account.
Because of this, a single magic mutation may be used to add money to two wallets on different bars or banking accounts.

To give a better picture of what this magic mutation might be used for, take a look at the following use case:  
In a given BARbapAPPa setup, users have a default wallet balance of €10,-.
The balance of an initialized wallet is zero. To get to default balance specified, a transaction must be used.
Of course, this transaction would contain a wallet mutation to add €10 to the user's wallet.
Because the money sum of a transaction must be zero, the money has to flow from somewhere.
For this specific case, the second mutation might be a magic mutation.
This because it doesn't fit the other mutation types,
and because from the systems perspective the money would appear to be _spawned from thin air_.

## Payment
A payment defines a single transaction that is made through a third party payment service.
This could be through PayPal, through iDeal, it could be a manual bank transfer or whatever is available for the relevant bank.

This payment is attached to a payment mutation for a transaction.

The _payment model_ has a state value:  
- Pending
- Processing
- Success
- Failure

Additional data might be attached for some payment methods when relevant.
Think of PayPal specific transaction IDs or an IBAN a user might be sending money from.

When the state of the transaction is changed by the payment service, the state of this payment should be changed accordingly.  
The state of any related payment mutations should be updated along with it.