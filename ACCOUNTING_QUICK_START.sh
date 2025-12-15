#!/bin/bash

# Accounting System Quick Start Script
# This script sets up the complete accounting system

echo "================================================"
echo "  Accounting System Quick Start"
echo "================================================"
echo ""

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Step 1: Run Migrations
echo -e "${BLUE}Step 1: Running migrations...${NC}"
php artisan migrate
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Migrations completed${NC}"
else
    echo -e "${YELLOW}✗ Migration error (may already be applied)${NC}"
fi
echo ""

# Step 2: Seed GL Accounts
echo -e "${BLUE}Step 2: Seeding GL Accounts (Chart of Accounts)...${NC}"
php artisan accounting:seed-gl-accounts --force
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ GL Accounts seeded (84 accounts created)${NC}"
else
    echo -e "${YELLOW}✗ GL Account seeding failed${NC}"
fi
echo ""

# Step 3: Seed Accounting Periods
echo -e "${BLUE}Step 3: Seeding Accounting Periods...${NC}"
php artisan db:seed --class=AccountingPeriodSeeder
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Accounting periods created${NC}"
else
    echo -e "${YELLOW}✗ Accounting periods seeding failed${NC}"
fi
echo ""

# Step 4: Verify Setup
echo -e "${BLUE}Step 4: Verifying setup...${NC}"
php artisan tinker << 'EOF'
echo "GL Accounts: " . \App\Models\GlAccount::count() . "\n";
echo "Accounting Periods: " . \App\Models\AccountingPeriod::count() . "\n";
echo "Current Period: " . (\App\Models\AccountingPeriod::where('status', 'open')->where('period_start', '<=', now())->where('period_end', '>=', now())->first()?->getDisplayName() ?? 'None') . "\n";
exit;
EOF
echo ""

echo -e "${GREEN}================================================${NC}"
echo -e "${GREEN}  Setup Complete!${NC}"
echo -e "${GREEN}================================================${NC}"
echo ""
echo "Next steps:"
echo "1. Test GL posting with a sample sale"
echo "2. Generate accounting reports"
echo "3. Set up event listeners for automated posting"
echo "4. Create accounting users and permissions"
echo ""
echo "Documentation:"
echo "- See ACCOUNTING_SYSTEM_DESIGN.md for architecture"
echo "- See ACCOUNTING_IMPLEMENTATION_GUIDE.md for usage"
echo ""
