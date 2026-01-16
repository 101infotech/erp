# Frontend Quick Reference - Enhanced Leads Module

## Quick Start

### 1. Install Dependencies
```bash
cd frontend
npm install
```

### 2. Configure API Base URL
Edit `frontend/src/services/api.config.js`:
```javascript
API_CONFIG = {
  BASE_URL: 'http://localhost:8000',  // Change this to your API URL
  LEADS_BASE: '/api/leads',
  TOKEN_KEY: 'auth_token'
}
```

### 3. Start Development Server
```bash
npm run dev
```

---

## Component Quick Reference

### Main Components
| Component | Purpose | Import Path |
|-----------|---------|-------------|
| LeadDashboard | Main container with all subcomponents | `components/Leads/LeadDashboard` |
| LeadList | Paginated table with filtering | `components/Leads/LeadList` |
| LeadForm | Create/Edit lead modal | `components/Leads/LeadForm` |
| LeadDetailsModal | Detailed view with 4 tabs | `components/Leads/LeadDetailsModal` |
| FollowUpList | Follow-up tracking | `components/Leads/FollowUpList` |
| PaymentList | Payment tracking | `components/Leads/PaymentList` |
| DocumentList | File upload/management | `components/Leads/DocumentList` |
| StatisticsCards | KPI display | `components/Leads/StatisticsCards` |
| AnalyticsDashboard | Analytics & reporting | `components/Leads/AnalyticsDashboard` |
| PipelineVisualization | Kanban board | `components/Leads/PipelineVisualization` |

### Using Components

**Import Single Component**:
```javascript
import { LeadDashboard } from '@/components/Leads';

function App() {
  return <LeadDashboard />;
}
```

**Import Multiple Components**:
```javascript
import { 
  LeadDashboard,
  AnalyticsDashboard,
  PipelineVisualization
} from '@/components/Leads';
```

---

## Custom Hooks Quick Reference

### Available Hooks
```javascript
import {
  useLeads,           // Main lead management
  useLead,            // Single lead with relationships
  useLeadStatistics,  // Dashboard statistics
  useFollowUps,       // Follow-up management
  usePayments,        // Payment management
  useDocuments,       // Document management
  useStages,          // Pipeline stages
  usePipeline,        // Full pipeline with leads
  useAnalytics        // Analytics data
} from '@/hooks/useLeads';
```

### Hook Usage Examples

**Using useLeads**:
```javascript
const { 
  leads, 
  pagination, 
  loading, 
  error,
  fetchLeads,
  createLead,
  updateLead,
  deleteLead
} = useLeads();

// Fetch leads with pagination
useEffect(() => {
  fetchLeads(1, { priority: 'high' });
}, []);

// Create new lead
const handleCreate = async (data) => {
  try {
    await createLead(data);
    // Lead created successfully
  } catch (err) {
    console.error('Error creating lead:', err);
  }
};
```

**Using useLead**:
```javascript
const { lead, loading, error, setLead } = useLead(leadId);

// Lead includes all relationships:
// - followUps: []
// - payments: []
// - documents: []
// - stage: {}
```

**Using useAnalytics**:
```javascript
const { 
  dashboard,      // KPI data
  pipelineData,   // Stage-wise data
  salesTeam,      // Team performance
  payments,       // Payment metrics
  loading,
  error,
  refetch
} = useAnalytics();
```

---

## API Service Layer

### leadsApi.js Functions

**Lead Operations**:
```javascript
import { 
  fetchLeads,
  fetchLead,
  createLead,
  updateLead,
  deleteLead,
  transitionLeadStage,
  bulkUpdateLeads,
  bulkDeleteLeads
} from '@/services/leadsApi';

// Fetch all leads with filters
const leads = await fetchLeads(1, 10, { priority: 'urgent' });

// Fetch single lead
const lead = await fetchLead(123);

// Create lead
const newLead = await createLead({
  company_name: 'ABC Corp',
  contact_person: 'John Doe',
  email: 'john@abc.com',
  phone: '9876543210',
  service_type: 'Consulting',
  priority: 'high'
});

// Update lead
await updateLead(123, { priority: 'urgent' });

// Delete lead
await deleteLead(123);

// Transition lead to different stage
await transitionLeadStage(123, stageId);
```

**Follow-up Operations**:
```javascript
import { 
  fetchFollowUps,
  createFollowUp,
  updateFollowUp,
  deleteFollowUp
} from '@/services/leadsApi';

// Fetch follow-ups for a lead
const followUps = await fetchFollowUps(leadId);

// Add follow-up
await createFollowUp(leadId, {
  type: 'call',
  date: '2024-01-15',
  notes: 'Discussed pricing',
  outcome: 'Interested'
});

// Update follow-up
await updateFollowUp(leadId, followUpId, { outcome: 'Not interested' });

// Delete follow-up
await deleteFollowUp(leadId, followUpId);
```

**Payment Operations**:
```javascript
import { 
  fetchPayments,
  createPayment,
  updatePayment,
  deletePayment,
  fetchPaymentSummary
} from '@/services/leadsApi';

// Fetch payments
const payments = await fetchPayments(leadId);

// Get payment summary
const summary = await fetchPaymentSummary(leadId);
// Returns: { quoted, paid, pending, percentage }

// Record payment
await createPayment(leadId, {
  amount: 5000,
  method: 'bank_transfer',
  date: '2024-01-15',
  reference: 'TXN12345'
});

// Update payment
await updatePayment(leadId, paymentId, { amount: 6000 });

// Delete payment
await deletePayment(leadId, paymentId);
```

**Document Operations**:
```javascript
import { 
  fetchDocuments,
  uploadDocument,
  updateDocument,
  deleteDocument,
  downloadDocument
} from '@/services/leadsApi';

// Fetch documents
const documents = await fetchDocuments(leadId);

// Upload document (FormData)
const formData = new FormData();
formData.append('file', fileObject);
formData.append('type', 'contract');
formData.append('description', 'Service agreement');

await uploadDocument(leadId, formData);

// Update document metadata
await updateDocument(leadId, documentId, { 
  description: 'Updated agreement' 
});

// Delete document
await deleteDocument(leadId, documentId);

// Download document
downloadDocument(leadId, documentId);  // Triggers browser download
```

**Analytics Operations**:
```javascript
import { 
  fetchDashboard,
  fetchPipelineAnalytics,
  fetchSalesTeamAnalytics,
  fetchPaymentAnalytics
} from '@/services/leadsApi';

// Dashboard data
const dashboard = await fetchDashboard();
// Returns: { total_leads, open_leads, closed_leads, conversion_rate, ... }

// Pipeline data
const pipeline = await fetchPipelineAnalytics();
// Returns: { stages: [...], total_value, avg_deal_size, ... }

// Team performance
const team = await fetchSalesTeamAnalytics();
// Returns: { team: [{ name, lead_count, closed_count, ... }] }

// Payment metrics
const payments = await fetchPaymentAnalytics();
// Returns: { total_quoted, total_received, total_pending, ... }
```

---

## Authentication Setup

### 1. Store Token After Login
```javascript
// After successful login API call
const response = await fetch('/api/login', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ email, password })
});

const data = await response.json();
localStorage.setItem('auth_token', data.token);

// Token is now automatically included in all API calls
```

### 2. Logout
```javascript
function logout() {
  localStorage.removeItem('auth_token');
  // Redirect to login
}
```

### 3. Check Authentication Status
```javascript
function isAuthenticated() {
  return !!localStorage.getItem('auth_token');
}
```

---

## Form Data Structure

### Create/Update Lead
```javascript
{
  company_name: "ABC Corporation",
  contact_person: "John Doe",
  email: "john@abc.com",
  phone: "9876543210",
  phone_secondary: "9876543211",  // optional
  service_type: "Consulting",
  service_description: "Full audit and consulting services",
  lead_source: "Website",  // optional
  priority: "high",  // low, medium, high, urgent
  quoted_amount: 100000,  // optional
  advance_amount: 10000,  // optional
  notes: "Referred by existing client"  // optional
}
```

### Create Follow-up
```javascript
{
  type: "call",  // call, visit, whatsapp, email, sms, meeting, other
  date: "2024-01-15",
  notes: "Discussed pricing and timeline",
  outcome: "Positive - waiting for approval"  // optional
}
```

### Record Payment
```javascript
{
  amount: 5000,
  method: "bank_transfer",  // cash, cheque, bank_transfer, upi, credit_card, debit_card, other
  date: "2024-01-15",
  reference: "TXN123456789"  // optional, should be unique
}
```

### Upload Document
FormData:
```javascript
{
  file: File,  // The file object
  type: "contract",  // photo, design, contract, quotation, report, invoice, proposal, other
  description: "Service agreement dated Jan 2024"  // optional
}
```

---

## Error Handling

### In Components
```javascript
function MyComponent() {
  const { leads, error, loading } = useLeads();

  if (loading) return <div>Loading...</div>;
  
  if (error) {
    return (
      <div className="error-alert">
        <p>Error: {error}</p>
        <button onClick={() => refetch()}>Retry</button>
      </div>
    );
  }

  return <div>{/* Render leads */}</div>;
}
```

### In API Calls
```javascript
async function handleCreate(data) {
  try {
    const lead = await createLead(data);
    console.log('Lead created:', lead);
    // Refresh list or show success
  } catch (error) {
    console.error('Failed to create lead:', error);
    // Show error message to user
    setError(error.message);
  }
}
```

---

## Styling Customization

### Component CSS Files
Each component has its own CSS file in `frontend/src/components/Leads/`:
- `LeadDashboard.css` - Main layout
- `LeadList.css` - Table styling
- `LeadForm.css` - Form styling
- etc.

### Global Color Variables
Edit the color values in any CSS file:
```css
/* Primary Colors */
--primary: #2d3748;
--primary-light: #4299e1;
--success: #38a169;
--warning: #d69e2e;
--danger: #c53030;
```

### Responsive Design
All components are responsive across:
- Desktop (1024px+)
- Tablet (768px - 1023px)
- Mobile (< 768px)

---

## Common Tasks

### Add New Field to Lead Form
1. Edit `LeadForm.jsx`:
   - Add form group in JSX
   - Add field to form submission data

2. Update `LeadList.jsx`:
   - Add column to table display (optional)

3. Update `leadsApi.js`:
   - Ensure field is included in createLead/updateLead

### Add New Lead Filter
1. Edit `LeadList.jsx`:
   - Add filter select/input in filter panel
   - Update onFilterChange handler

2. Update `useLeads.js`:
   - Update setFilters to include new filter
   - Pass filter to fetchLeads API call

### Customize Analytics Dashboard
1. Edit `AnalyticsDashboard.jsx`:
   - Modify cards displayed
   - Change data visualization

2. Update `useAnalytics()` hook:
   - Fetch additional data from new endpoints

### Add Export Functionality
1. Create export button in component
2. Use data from hooks
3. Format as CSV/Excel/PDF
4. Trigger browser download

---

## Debugging Tips

### Enable API Logging
```javascript
// In api.config.js, add before fetch:
console.log('API Call:', url, options);
```

### Check Redux DevTools (if Redux added later)
- Install Redux DevTools Extension
- Monitor state changes in real-time

### Browser Console Errors
1. Check authentication token is set
2. Verify API_CONFIG.BASE_URL is correct
3. Check CORS settings on backend
4. Review network tab for 401/403/404 errors

### Component State Debugging
```javascript
// Add this in any component to see state
useEffect(() => {
  console.log('State updated:', { leads, loading, error });
}, [leads, loading, error]);
```

---

## File Structure Reference

```
frontend/src/
├── components/
│   └── Leads/
│       ├── LeadDashboard.jsx
│       ├── LeadDashboard.css
│       ├── LeadList.jsx
│       ├── LeadList.css
│       ├── ... (10 components total)
│       └── index.js
├── services/
│   ├── api.config.js
│   ├── leadsApi.js
│   └── index.js
├── hooks/
│   ├── useLeads.js
│   └── index.js
├── App.jsx
├── index.css
└── main.jsx
```

---

## Performance Tips

1. **Pagination**: Use pagination to limit leads loaded at once
2. **Filtering**: Apply filters to reduce data transfer
3. **Lazy Loading**: Load details only when user requests
4. **Memoization**: Components re-render only when props change
5. **Caching**: API responses are cached in hooks

---

## Next Steps

1. ✅ Integrate into main app router
2. ✅ Set up authentication/login flow
3. ✅ Configure API base URL for your environment
4. ⏳ Add toast notifications for user feedback
5. ⏳ Add confirmation dialogs for delete operations
6. ⏳ Add loading skeletons for better UX
7. ⏳ Create unit and integration tests
8. ⏳ Add analytics charts (Chart.js/Recharts)

---

**Last Updated**: Current Session
**Version**: Phase 3 Frontend Complete (10/10 Components)
**Status**: Ready for Integration & Testing
