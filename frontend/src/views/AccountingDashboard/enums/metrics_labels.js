export const metricsLabels = {
  company_name: { name: 'Company Name' },
  A_pdf_requests_singleorder_none_updateprior: {
    name: 'Single-order active PDF requests',
    bilable: true,
    formula: 'Total Number of PDF Requests (excluding rejected and deleted requests) with only one order assigned to them - Number of active single-order PDF requests where the order is a revision'
  },
  B_pdf_requests_multiorder_less_all_updateprior: {
    name: 'Multi-order active PDF requests',
    bilable: true,
    formula: 'Total Number of PDF Requests (excluding rejected and deleted requests) with more than one order assigned to them - Number of active multi-order PDF requests where ALL orders are revisions'
  },
  D_datafile_requests_none_updateprior: {
    name: 'Active CSV/Tabular requests',
    bilable: true,
    formula: 'Total Number of datafile Requests (excluding rejected and deleted requests) - requests where all are revisions'
  },
  E_requests_all_updateprior: {
    name: 'Requests, where all orders are revisions',
    formula: 'Number of all active requests in the company where all orders in a request are revised orders'
  },
  F_requests_rejected: { name: 'Rejected Requests', formula: 'Sum of all rejected requests for the company' },
  G_requests: { name: 'Total Number of Requests', formula: 'All requests for the company. Formerly called <strong>“Number of requests”</strong>' },
  H_pdf_orders_less_requests_anyupdateprior: {
    name: 'Total Number of Requests',
    bilable: true,
    formula: '(Number of orders from pdf - number of orders that were updated before (with existing reference number) ) - number of pdf requests (excluding requests where all orders are updates)'
  },
  I_datafile_orders_less_requests_anyupdateprior: {
    name: 'Additional CSV / Tabular Orders',
    bilable: true,
    formula: '(Number of orders from datafile - number of orders that were updated before (with existing reference number) ) - number of datafile requests (excluding requests where all orders are updates)'
  },
  J_pdf_orders_updateprior: { name: 'Additional CSV / Tabular Orders', bilable: true, formula: 'Number pdf of orders with prior updates' },
  K_datafile_orders_updateprior: { name: 'CSV/Tabular Order Revisions', bilable: true, formula: 'Number datafile of orders with prior updates' },
  L_pdf_pages_overages: { name: 'PDF Pages Overages', bilable: true, formula: 'Number of pdf pages  - (number of all pdf orders x 2)' },
  M_tms_shipments: { name: 'Number of TMS Shipments', formula: 'All TMS Shipments for the Company' },
  N_requests_deleted: { name: 'Deleted Requests', formula: 'Sum of all deleted requests for the company.' },
  O_pdf_orders_including_deleted: { name: 'Total Number of PDF Orders', formula: 'All pdf orders of the company (incl deleted and rejected)' },
  P_datafile_orders_including_deleted: { name: 'Total Number of CSV Orders', formula: 'All pdf orders of the company (incl deleted and rejected)' },
  Q_pdf_pages_including_deleted: { name: 'Total Number of PDF Pages', formula: 'All pdf pages of the company (incl deleted and rejected)' },
  // requests_mixed_updateprior: { name: null },
  // requests_none_updateprior: { name: null },
  // pdf_requests: { name: null },
  // pdf_requests_all_updateprior: { name: null },
  // pdf_requests_mixed_updateprior: { name: null },
  // pdf_requests_none_updateprior: { name: null },
  // pdf_requests_singleorder: { name: null },
  // pdf_requests_singleorder_all_updateprior: { name: null },
  // pdf_requests_singleorder_mixed_updateprior: { name: null },
  // pdf_requests_multiorder: { name: null },
  // pdf_requests_multiorder_all_updateprior: { name: null },
  // pdf_requests_multiorder_mixed_updateprior: { name: null },
  // pdf_requests_multiorder_none_updateprior: { name: null },
  // pdf_orders: { name: null },
  // pdf_orders_dontupdateprior: { name: null },
  // datafile_requests: { name: null },
  // datafile_requests_all_updateprior: { name: null },
  // datafile_requests_mixed_updateprior: { name: null },
  // datafile_orders: { name: null },
  // datafile_orders_dontupdateprior: { name: null },
  // orders_deleted: { name: null },
  // pdf_orders_deleted: { name: null },
  // datafile_orders_deleted: { name: null },
  // pdf_requests_deleted: { name: null },
  // datafile_requests_deleted: { name: null },
  // orders: { name: null },
}
