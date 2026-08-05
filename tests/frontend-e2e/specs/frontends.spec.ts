import { expect, test } from '@playwright/test'

test('customer frontend renders core navigation and RTL document', async ({ page }) => {
  await page.goto('http://127.0.0.1:3001/dashboard')
  await expect(page.locator('html')).toHaveAttribute('dir', 'rtl')
  await expect(page.getByRole('link', { name: 'دارایی‌ها' })).toBeVisible()
  await expect(page.getByRole('link', { name: 'سفارش‌ها' })).toBeVisible()
  await expect(page.getByRole('link', { name: 'امانات' })).toBeVisible()
  await expect(page.getByRole('link', { name: 'حساب من' })).toBeVisible()
})

test('admin operator frontend renders both protected-area entry points', async ({ page }) => {
  await page.goto('http://127.0.0.1:3002/admin')
  await expect(page.locator('html')).toHaveAttribute('dir', 'rtl')
  await expect(page.getByRole('link', { name: 'مدیریت' })).toBeVisible()
  await expect(page.getByRole('link', { name: 'اپراتور' })).toBeVisible()
  await expect(page.getByText('دسترسی توسط Backend کنترل می‌شود')).toBeVisible()
})
