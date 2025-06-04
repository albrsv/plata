export interface Transaction {
    id: number;
    type: string;
    amount: number;
    status: string;
    comment: string;
    created_at: string;
}