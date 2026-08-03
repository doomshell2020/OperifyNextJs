import apiClient from './apiClient';

// ─── Types ────────────────────────────────────────────────────────────────────

export interface IndentItem {
  id: number;
  item_id: number;
  item_name: string;
  size_name: string;
  unit_name: string;
  category_name: string;
  quantity: number;
  return_qty: number;
  sale_price: number;
  amount: number;
  stock_in_hand?: number;
}

export interface IndentSummary {
  indent_id: number;
  added_time: string;
  created_by: string;
  total_qty: number;
  item_count: number;
  items: IndentItem[];
}

export interface PendingIndent {
  indent_id: number;
  added_time: string;
  created_by: string;
  total_qty: number;
  remaining_qty: number;
  items: { id: number; item_id: number; item_name: string; quantity: number; return_qty: number }[];
}

export interface IndentDetail {
  items: IndentItem[];
  is_temp: boolean;
}

export interface ItemSearchResult {
  id: number;
  item_name: string;
  cost_price: number;
  size_id: number | null;
  size_name: string;
  unit_id: number;
  unit_name?: string;
}

export interface TempIndentItem {
  id: number;
  indent_id: number;
  item_id: number;
  item_name: string;
  size_name: string;
  unit_name: string;
  quantity: number;
  sale_price: number;
  amount: number;
}

// ─── Service ──────────────────────────────────────────────────────────────────

class IndentService {
  async getNextIndentId(): Promise<number> {
    const res = await apiClient.get('/indents/next-id');
    return res.data.data.next_id;
  }

  async searchItems(query: string): Promise<ItemSearchResult[]> {
    const res = await apiClient.get('/indents/items/search', { params: { q: query } });
    return res.data.data;
  }

  async addTempItem(data: {
    indent_id: number;
    item_id: number;
    size_id?: number | null;
    quantity: number;
  }): Promise<TempIndentItem> {
    const res = await apiClient.post('/indents/temp', data);
    return res.data.data;
  }

  async removeTempItem(id: number): Promise<void> {
    await apiClient.delete(`/indents/temp/${id}`);
  }

  async getTempItems(indent_id: number): Promise<TempIndentItem[]> {
    const res = await apiClient.get(`/indents/temp/${indent_id}`);
    return res.data.data;
  }

  async finalizeIndent(indent_id: number): Promise<void> {
    await apiClient.post('/indents/finalize', { indent_id });
  }

  async listIndents(filters?: {
    indent_id?: string;
    date_from?: string;
    date_to?: string;
  }): Promise<IndentSummary[]> {
    const res = await apiClient.get('/indents', { params: filters });
    return res.data.data;
  }

  async getPendingIndents(): Promise<PendingIndent[]> {
    const res = await apiClient.get('/indents/pending');
    return res.data.data;
  }

  async getIndentDetail(indent_id: number | string): Promise<IndentDetail> {
    const res = await apiClient.get(`/indents/${indent_id}/detail`);
    return res.data.data;
  }
}

export default new IndentService();
